# FlippDeal Escrow System

A comprehensive escrow workflow implementation for domain marketplace transactions, ensuring secure payments and domain transfers.

## Overview

The escrow system provides a secure transaction flow where buyer payments are held in escrow until domain transfer is confirmed. This protects both buyers and sellers by ensuring payment security and transfer verification.

## Features

### Core Functionality
- **Secure Payment Processing**: PayPal integration with webhook support
- **Escrow Management**: Funds held until transfer verification
- **Domain Transfer Verification**: Multiple verification methods (DNS, Registrar, Manual, Auth Code)
- **Automated Verification**: Background jobs for automated transfer checks
- **Admin Controls**: Complete admin interface for escrow management
- **Audit Trail**: Comprehensive logging of all escrow activities

### Transaction States
- `pending` - Waiting for buyer payment
- `in_escrow` - Payment captured and held
- `released` - Funds released to seller
- `refunded` - Funds returned to buyer
- `cancelled` - Transaction cancelled

## Database Schema

### Transactions Table
```sql
- id (Primary Key)
- buyer_id (Foreign Key to users)
- seller_id (Foreign Key to users)
- domain_id (Foreign Key to domains)
- amount (Decimal 15,2)
- fee_amount (Decimal 15,2) - Platform commission
- currency (String 3) - Default USD
- provider (String 50) - Payment provider
- provider_txn_id (String) - Provider transaction ID
- escrow_state (Enum) - Transaction state
- escrow_metadata (JSON) - Additional data
- escrow_release_by_admin_id (Foreign Key to users)
- escrow_released_at (Timestamp)
- refunded_at (Timestamp)
- refund_reason (Text)
- created_at, updated_at
```

### Domain Transfers Table
```sql
- id (Primary Key)
- domain_id (Foreign Key to domains)
- transaction_id (Foreign Key to transactions)
- from_user_id (Foreign Key to users)
- to_user_id (Foreign Key to users)
- transfer_method (String 50) - Transfer method
- evidence_data (JSON) - Transfer evidence
- evidence_url (String) - File URL
- transfer_notes (Text)
- verified (Boolean)
- verified_by_admin_id (Foreign Key to users)
- verified_at (Timestamp)
- verification_notes (Text)
- created_at, updated_at
```

### Audits Table
```sql
- id (Primary Key)
- auditable_type (String) - Model class
- auditable_id (Big Integer) - Model ID
- event (String) - Event type
- old_values (JSON)
- new_values (JSON)
- user_id (Foreign Key to users)
- user_type (String) - user/admin/system
- description (Text)
- metadata (JSON)
- ip_address (String 45)
- user_agent (String)
- created_at, updated_at
```

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Configure PayPal
Update your `.env` file with PayPal credentials:
```env
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_MODE=sandbox # or 'live' for production
```

### 3. Configure Webhooks
Set up PayPal webhooks pointing to:
```
https://yourdomain.com/webhook/payments/paypal
```

### 4. Queue Configuration
Ensure your queue is running for background jobs:
```bash
php artisan queue:work
```

## Usage

### For Buyers

#### 1. Purchase a Domain
```php
// Buy-Now Purchase
POST /domains/{domain}/buy-now
{
    "payment_method": "paypal"
}

// Auction Win
POST /domains/{domain}/auction-win
```

#### 2. View Purchase Status
```php
GET /my-purchases
GET /transactions/{transaction}/buyer-status
```

### For Sellers

#### 1. Submit Transfer Evidence
```php
POST /transactions/{transaction}/transfer
{
    "transfer_method": "registrar",
    "transfer_notes": "Transfer initiated with GoDaddy",
    "evidence_data": {
        "registrar": "GoDaddy",
        "transfer_id": "TXN123456",
        "auth_code": "ABC123XYZ"
    },
    "evidence_file": "file_upload"
}
```

#### 2. View Sales Status
```php
GET /my-sales
GET /transactions/{transaction}/transfer
```

### For Admins

#### 1. Manage Escrow
```php
GET /admin/escrow                    # View all transactions
GET /admin/escrow/{transaction}      # View transaction details
POST /admin/escrow/{transaction}/release  # Release escrow
POST /admin/escrow/{transaction}/refund   # Refund transaction
```

#### 2. Verify Transfers
```php
GET /admin/escrow/transfers/pending  # View pending transfers
POST /admin/escrow/transfers/{transfer}/verify  # Verify transfer
```

## API Endpoints

### Webhooks
- `POST /webhook/payments/paypal` - PayPal payment webhook
- `POST /webhook/payments/test` - Test webhook for development

### Purchase Flow
- `GET /domains/{domain}/purchase` - Show purchase form
- `POST /domains/{domain}/buy-now` - Process buy-now
- `POST /domains/{domain}/auction-win` - Process auction win
- `GET /purchase/success` - Payment success handler
- `GET /purchase/cancel` - Payment cancel handler

### Transfer Management
- `GET /transactions/{transaction}/transfer` - Show transfer form
- `POST /transactions/{transaction}/transfer` - Submit transfer evidence
- `GET /transactions/{transaction}/transfer-status` - Get transfer status

## Services

### EscrowService
Main service for managing escrow transactions:

```php
// Create transaction
$transaction = $escrowService->createEscrowTransaction($buyer, $seller, $domain, $amount, $provider);

// Mark as in escrow
$escrowService->markInEscrow($transaction, $providerTxnId, $metadata);

// Release escrow
$escrowService->releaseEscrow($transaction, $adminId, $metadata);

// Refund escrow
$escrowService->refundEscrow($transaction, $reason, $metadata);

// Create transfer
$transfer = $escrowService->createDomainTransfer($transaction, $method, $evidenceData, $evidenceUrl, $notes);

// Verify transfer
$escrowService->verifyDomainTransfer($transfer, $adminId, $notes);
```

### PayPalService
Extended with payout and refund capabilities:

```php
// Create payout
$result = $paypalService->createPayout([
    'email' => $seller->paypal_email,
    'amount' => $transaction->net_amount,
    'currency' => 'USD',
    'note' => 'Domain sale payout'
]);

// Create refund
$result = $paypalService->createRefund([
    'transaction_id' => $transaction->provider_txn_id,
    'amount' => $transaction->amount,
    'currency' => 'USD',
    'reason' => 'Domain transfer issue'
]);
```

## Background Jobs

### ProcessPayout
Handles automatic payouts to sellers when escrow is released.

### ProcessRefund
Handles automatic refunds to buyers when transactions are refunded.

### VerifyTransferJob
Automatically verifies domain transfers based on evidence provided.

## Events

### TransactionCreated
Fired when a new escrow transaction is created.

### TransactionInEscrow
Fired when payment is confirmed and funds are held in escrow.

### TransactionReleased
Fired when escrow funds are released to the seller.

### TransactionRefunded
Fired when a transaction is refunded.

## Testing

### Run Test Script
```bash
php test_escrow_system.php
```

### Test Webhook
```bash
curl -X POST http://localhost:8000/webhook/payments/test \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_id": 1,
    "event_type": "payment_completed",
    "provider_transaction_id": "PAYPAL_TXN_123456"
  }'
```

## Security Features

### Webhook Security
- Signature verification (implemented in production)
- Replay attack protection
- IP whitelisting (recommended)

### Data Protection
- Encrypted sensitive data in `escrow_metadata`
- Secure file uploads for transfer evidence
- Audit logging for all actions

### Access Control
- Role-based permissions
- Transaction ownership verification
- Admin-only escrow management

## Monitoring & Logging

### Audit Trail
All escrow activities are logged in the `audits` table with:
- User who performed the action
- Timestamp
- Old and new values
- IP address and user agent
- Description of the action

### Error Handling
- Comprehensive error logging
- Graceful failure handling
- Retry mechanisms for failed jobs

## Production Considerations

### PayPal Configuration
1. Switch to live mode in `.env`
2. Update webhook URLs to production domain
3. Verify webhook signatures
4. Test with small amounts first

### Queue Management
1. Use Redis or database queue driver
2. Set up queue monitoring
3. Implement dead letter queues
4. Monitor job failures

### Database Optimization
1. Add indexes for frequently queried fields
2. Archive old audit logs
3. Monitor query performance
4. Set up database backups

### Security Hardening
1. Enable webhook signature verification
2. Implement rate limiting
3. Use HTTPS for all endpoints
4. Regular security audits

## Troubleshooting

### Common Issues

#### Transaction Stuck in Pending
- Check PayPal webhook configuration
- Verify webhook endpoint is accessible
- Check queue is processing jobs

#### Transfer Verification Fails
- Verify evidence data format
- Check domain ownership records
- Review verification job logs

#### Payout/Refund Failures
- Verify seller PayPal email
- Check PayPal API credentials
- Review error logs for details

### Debug Mode
Enable detailed logging by setting:
```env
LOG_LEVEL=debug
QUEUE_LOG_LEVEL=debug
```

## Support

For technical support or questions about the escrow system:
1. Check the audit logs for transaction history
2. Review application logs for error details
3. Test with the provided test script
4. Contact system administrator for complex issues

## License

This escrow system is part of the FlippDeal domain marketplace platform.
