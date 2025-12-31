# Stripe Balance Availability Fix - Summary

## Problem
Transfers were failing with "insufficient funds" errors because Stripe funds are still pending after a payment succeeds. This is expected Stripe behavior - funds take time to clear and become available for transfers.

## Solution
Implemented a balance availability check system that:
1. Marks deals as `awaiting_funds` after payment succeeds
2. Checks Stripe balance before attempting transfers
3. Blocks transfers until funds are available
4. Shows appropriate UI messages to users

## Changes Made

### 1. Database Changes

#### Added `awaiting_funds` Field to `deals` Table
- Boolean field to track if Stripe funds are still pending
- Defaults to `false`
- Set to `true` when payment succeeds (funds may still be pending)
- Set to `false` when funds are available and transfer succeeds

### 2. StripeService Updates

#### Added `getAvailableBalance()` Method
- Fetches Stripe account balance using `\Stripe\Balance::retrieve()`
- Returns available balance in dollars (converts from cents)
- Logs available and pending balances for debugging

### 3. Payment Flow Updates

#### After Payment Succeeds (Webhook)
- `payment_status` → `paid_escrowed`
- `awaiting_funds` → `true` (funds may still be pending in Stripe)

#### Before Creating Transfer
1. Check Stripe available balance
2. Compare with `athlete_net_payout` amount
3. If insufficient:
   - Keep `awaiting_funds = true`
   - Block transfer
   - Show user-friendly error message
4. If sufficient:
   - Set `awaiting_funds = false`
   - Proceed with transfer

#### After Transfer Succeeds
- `awaiting_funds` → `false`
- `payment_status` → `released`

### 4. Deal Model Updates

#### Updated Methods
- `isInEscrow()`: Now checks for `paid_escrowed` status
- `canBeReleased()`: Blocks release if `awaiting_funds = true`
- `getEscrowStatus()`: Shows "Payout Pending Clearing" when `awaiting_funds = true`

### 5. UI Updates

#### Business View (`deals/index.blade.php`)
- Shows "Payout Pending Clearing" badge when `awaiting_funds = true`
- Release button is disabled when `awaiting_funds = true` (via `canBeReleased()`)

#### Error Messages
- Business: "Payment complete – payout pending clearing. Stripe funds are still pending. The transfer will be processed automatically once funds are available. Please check back later."
- Athlete: "Earnings pending" (shown via `getEscrowStatus()`)

### 6. Release Payment Controller Updates

#### Added Balance Check
- Checks Stripe balance BEFORE creating payout record
- Prevents "insufficient funds" errors
- Updates `awaiting_funds` status appropriately
- Provides clear error messages

## Key Features

✅ **Balance Verification**: Checks Stripe balance before transfers
✅ **Status Tracking**: `awaiting_funds` flag tracks pending funds
✅ **User-Friendly Messages**: Clear messages explaining pending status
✅ **Automatic Processing**: Once funds are available, transfer can proceed
✅ **Error Prevention**: Blocks transfers when funds aren't available

## Files Changed

1. `database/migrations/2025_12_31_085455_add_awaiting_funds_to_deals_table.php` - New field
2. `app/Models/Deal.php` - Added `awaiting_funds` to fillable and casts, updated methods
3. `app/Services/StripeService.php` - Added `getAvailableBalance()` method
4. `app/Http/Controllers/StripeWebhookController.php` - Sets `awaiting_funds = true` on payment success
5. `app/Http/Controllers/PaymentController.php` - Added balance check before transfer
6. `resources/views/deals/index.blade.php` - Shows "Payout Pending Clearing" status

## Testing Checklist

- [ ] Create deal with Stripe payment → Should mark as `awaiting_funds = true`
- [ ] Try to release payment immediately → Should be blocked with "pending clearing" message
- [ ] Wait for Stripe funds to clear → Check balance is sufficient
- [ ] Release payment after funds clear → Should succeed
- [ ] Verify `awaiting_funds` is set to `false` after successful transfer
- [ ] Check UI shows correct status messages

## Migration Steps

1. Run migration:
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

- **Funds are pending**: Stripe funds take time to clear (typically 2-7 days for new accounts, instant for established accounts)
- **Balance check is required**: Always check balance before creating transfers
- **Status is tracked**: `awaiting_funds` flag prevents premature transfer attempts
- **User experience**: Clear messages explain why transfers are pending
- **Automatic retry**: Users can retry release once funds are available (balance check will pass)

## Future Enhancements

- Consider adding a scheduled job to automatically retry transfers when funds become available
- Add webhook handler for `balance.available` events to automatically process pending transfers
- Add admin dashboard to view deals awaiting funds

