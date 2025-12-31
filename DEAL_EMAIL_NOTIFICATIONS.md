# Deal Email Notifications - Complete Flow

## Overview
This document outlines all email notifications sent throughout the deal process to ensure clear communication between businesses and athletes.

## Email Flow

### 1. **Deal Created** ✅
- **When:** Business creates and funds a deal
- **Recipient:** Athlete
- **Email Class:** `NewDealCreatedMail`
- **Location:** `app/Http/Controllers/DealController@store`
- **Template:** `resources/views/emails/new-deal-created.blade.php`
- **Subject:** "You've received a new deal"
- **Content:** Deal details, payment amount, link to view/accept deal

### 2. **Deal Accepted** ✅ NEW
- **When:** Athlete accepts the deal invitation
- **Recipient:** Business
- **Email Class:** `DealAcceptedMail`
- **Location:** `app/Http/Controllers/Athlete/DealController@accept`
- **Template:** `resources/views/emails/deal-accepted.blade.php`
- **Subject:** "Deal accepted by athlete"
- **Content:** Athlete name, deal details, deadline, link to view deal

### 3. **Deliverables Submitted** ✅
- **When:** Athlete submits work/deliverables for review
- **Recipient:** Business
- **Email Class:** `DeliverablesSubmittedMail`
- **Location:** `app/Http/Controllers/Athlete/DealController@submitDeliverables`
- **Template:** `resources/views/emails/deliverables-submitted.blade.php`
- **Subject:** "Deliverables submitted for review"
- **Content:** Athlete name, deal details, link to review deliverables

### 4. **Revisions Requested** ✅
- **When:** Business requests revisions on submitted work
- **Recipient:** Athlete
- **Email Class:** `RevisionsRequestedMail`
- **Location:** `app/Http/Controllers/DealApprovalController@requestRevisions`
- **Template:** `resources/views/emails/revisions-requested.blade.php`
- **Subject:** "Revisions requested on your submission"
- **Content:** Revision feedback, deal details, link to view deal and resubmit

### 5. **Payment Released** ✅
- **When:** Business approves work and payment is released from escrow
- **Recipient:** Athlete
- **Email Class:** `PaymentReleasedMail`
- **Location:** `app/Http/Controllers/PaymentController@releasePayment`
- **Template:** `resources/views/emails/payment-released.blade.php`
- **Subject:** "Your payment has been released"
- **Content:** Deal details, payout amount, link to view earnings

## Additional Email Notifications

### 6. **Withdrawal Requested** ✅
- **When:** Athlete requests a withdrawal from earnings
- **Recipient:** Athlete
- **Email Class:** `WithdrawalRequestedMail`
- **Location:** `app/Http/Controllers/Athlete/EarningsController@withdraw`
- **Template:** `resources/views/emails/withdrawal-requested.blade.php`
- **Subject:** "Withdrawal request received"
- **Content:** Withdrawal amount, confirmation message

## Email Features

All emails include:
- ✅ Consistent layout and branding
- ✅ Clear call-to-action buttons
- ✅ Fallback text links (for email clients that block images)
- ✅ High contrast design (white background, black text)
- ✅ Mobile-friendly responsive design
- ✅ Professional, calm tone
- ✅ No marketing language or emojis
- ✅ Proper error handling and logging

## Error Handling

All email sends are wrapped in try-catch blocks:
- ✅ Errors are logged with full details
- ✅ Email failures don't block user actions
- ✅ Success/failure is logged for debugging

## Testing Checklist

- [ ] Business creates deal → Athlete receives "Deal Created" email
- [ ] Athlete accepts deal → Business receives "Deal Accepted" email
- [ ] Athlete submits deliverables → Business receives "Deliverables Submitted" email
- [ ] Business requests revisions → Athlete receives "Revisions Requested" email
- [ ] Business approves work → Athlete receives "Payment Released" email
- [ ] Athlete requests withdrawal → Athlete receives "Withdrawal Requested" email

## Logging

All email sends are logged:
- Success: `\Log::info('Email sent', [...])`
- Failure: `\Log::error('Failed to send email', [...])`

Check `storage/logs/laravel.log` for email activity.

