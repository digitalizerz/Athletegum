<?php

namespace App\Services;

final class AthletePayoutReleaseResult
{
    public const OUTCOME_TRANSFER_INITIATED = 'transfer_initiated';

    public const OUTCOME_VALIDATION_ERROR = 'validation_error';

    /** Stripe balance: funds exist but still pending settlement */
    public const OUTCOME_SETTLEMENT_RETRY = 'settlement_retry';

    /** Not enough money in Stripe (available + pending) */
    public const OUTCOME_INSUFFICIENT_PLATFORM_BALANCE = 'insufficient_platform_balance';

    /** Transfer API failed due to insufficient available funds — retry later */
    public const OUTCOME_TRANSFER_FUNDS_RETRY = 'transfer_funds_retry';

    public const OUTCOME_STRIPE_ERROR = 'stripe_error';

    public const OUTCOME_GENERIC_ERROR = 'generic_error';

    public function __construct(
        public readonly string $outcome,
        public readonly ?string $message = null,
        public readonly int $retryAfterSeconds = 120,
    ) {}

    public static function transferInitiated(string $message): self
    {
        return new self(self::OUTCOME_TRANSFER_INITIATED, $message);
    }

    public static function validationError(string $message): self
    {
        return new self(self::OUTCOME_VALIDATION_ERROR, $message);
    }

    public static function settlementRetry(int $retryAfterSeconds = 120): self
    {
        return new self(self::OUTCOME_SETTLEMENT_RETRY, null, $retryAfterSeconds);
    }

    public static function insufficientPlatformBalance(string $message): self
    {
        return new self(self::OUTCOME_INSUFFICIENT_PLATFORM_BALANCE, $message);
    }

    public static function transferFundsRetry(string $message, int $retryAfterSeconds = 300): self
    {
        return new self(self::OUTCOME_TRANSFER_FUNDS_RETRY, $message, $retryAfterSeconds);
    }

    public static function stripeError(string $message): self
    {
        return new self(self::OUTCOME_STRIPE_ERROR, $message);
    }

    public static function genericError(string $message): self
    {
        return new self(self::OUTCOME_GENERIC_ERROR, $message);
    }
}
