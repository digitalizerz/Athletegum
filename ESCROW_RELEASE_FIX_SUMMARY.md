# Escrow Release Payment Fix - Summary

## Problem
The "Release Escrow Payment" action was failing because it tried to use Stripe's non-existent escrow/release concept. The code attempted to transfer from a specific charge using `source_transaction`, which doesn't work for wallet payments and is not the correct approach for Stripe Connect.

## Solution
Implemented escrow as **internal DB state only** and release payments by creating Stripe Transfers from platform balance to athlete's connected account.

## Changes Made

### 1. Database Changes

#### Created `payouts` Table
- Tracks all payouts to athletes
- Includes `idempotency_key` (unique) to prevent double payouts
- Stores `stripe_transfer_id`, amount, status, error messages
- Links to deal and athlete

#### Added `stripe_charge_id` to `deals` Table
- Stores charge ID (`ch_xxx`) for reference only
- Not used for transfers, just for tracking

### 2. Payment Status Flow

**Before:**
- Payment succeeds → `payment_status = 'paid'`

**After:**
- Payment succeeds → `payment_status = 'paid_escrowed'` (escrow is internal DB state)
- Release succeeds → `payment_status = 'released'`

### 3. Fee Calculation & Storage

**On Payment Success:**
- `compensation_amount` = gross amount (what business pays minus platform fee)
- `platform_fee_amount` = 10% of compensation (internal, platform keeps this)
- `athlete_fee_amount` = 5% of compensation (internal, deducted from athlete payout)
- `athlete_net_payout` = compensation - athlete_fee (what athlete receives)

**On Release:**
- Creates ONE Stripe Transfer using `athlete_net_payout`
- Stripe only sees the final transfer amount
- Platform fee and athlete fee are internal math only

### 4. Release Payment Logic

**New Flow:**
1. Validate deal status is `paid_escrowed` (or `paid` for backward compatibility)
2. Validate athlete has `stripe_account_id` (acct_xxx)
3. Check if payout already exists for this deal (prevent double pay)
4. Create payout record with idempotency key: `deal_{dealId}_release_v1`
5. Create Stripe Transfer from platform balance (NOT from charge)
   - Uses idempotency key to prevent duplicates
   - Works for both Stripe card payments AND wallet payments
6. Update payout record with transfer ID
7. Update deal status to `released`

### 5. StripeService Changes

**Before:**
```php
transferToAthlete($amount, $accountId, $chargeId, $metadata)
// Used source_transaction = $chargeId
```

**After:**
```php
transferToAthlete($amount, $accountId, $idempotencyKey, $metadata)
// Transfers from platform balance (no source_transaction)
// Uses idempotency key to prevent duplicates
```

### 6. Webhook Updates

- `payment_intent.succeeded` → Updates deal to `paid_escrowed`
- Stores `stripe_charge_id` for reference
- `charge.succeeded` → Also updates to `paid_escrowed` (backup)

## Key Features

✅ **Idempotency**: Uses unique idempotency keys to prevent double payouts
✅ **DB Guardrails**: Checks for existing payouts before creating transfers
✅ **Works for All Payment Types**: Both Stripe card payments and wallet payments
✅ **Proper Fee Handling**: Platform fee (10%) and athlete fee (5%) are internal only
✅ **Error Handling**: Comprehensive error messages and logging
✅ **Payout Tracking**: All payouts tracked in `payouts` table

## Files Changed

1. `database/migrations/2025_12_31_080044_create_payouts_table.php` - New payouts table
2. `database/migrations/2025_12_31_080123_add_stripe_charge_id_to_deals_table.php` - Add charge ID field
3. `app/Models/Payout.php` - New payout model
4. `app/Models/Deal.php` - Added `stripe_charge_id` to fillable
5. `app/Services/StripeService.php` - Updated `transferToAthlete()` to use idempotency keys
6. `app/Http/Controllers/PaymentController.php` - Completely rewritten `releasePayment()` method
7. `app/Http/Controllers/DealController.php` - Updated deal creation to store fees and mark as `paid_escrowed`
8. `app/Http/Controllers/StripeWebhookController.php` - Updated to mark as `paid_escrowed` and store charge ID

## Testing Checklist

- [ ] Create deal with Stripe card payment → Status should be `paid_escrowed`
- [ ] Create deal with wallet payment → Status should be `paid_escrowed`
- [ ] Release payment for Stripe card deal → Should create transfer successfully
- [ ] Release payment for wallet deal → Should create transfer successfully (NEW!)
- [ ] Try to release payment twice → Should be blocked by idempotency key
- [ ] Check `payouts` table → Should have record with transfer ID
- [ ] Check deal status → Should be `released` after successful transfer

## Migration Steps

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Clear caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. Test with a small deal first

## Important Notes

- **Escrow is internal only**: No Stripe escrow concept, just DB state
- **Fees are internal**: Platform fee (10%) and athlete fee (5%) are calculated and stored but not sent to Stripe
- **Transfers from platform balance**: All transfers come from platform's available balance, not from specific charges
- **Idempotency keys**: Format is `deal_{dealId}_release_v1` - change version if you need to allow re-releases
- **Wallet payments now work**: Previously couldn't release wallet payments, now they work because we transfer from platform balance

