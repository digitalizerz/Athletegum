<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'athlete_id',
        'type',
        'title',
        'message',
        'action_url',
        'deal_id',
        'message_id',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the user (SMB) this notification belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the athlete this notification belongs to
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the deal this notification is related to
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * Get the message this notification is related to
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Create a notification for a user (SMB)
     */
    public static function createForUser(int $userId, string $type, string $title, string $message, ?string $actionUrl = null, ?int $dealId = null, ?int $messageId = null): self
    {
        return self::create([
            'user_type' => 'user',
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'deal_id' => $dealId,
            'message_id' => $messageId,
        ]);
    }

    /**
     * Create a notification for an athlete
     */
    public static function createForAthlete(int $athleteId, string $type, string $title, string $message, ?string $actionUrl = null, ?int $dealId = null, ?int $messageId = null): self
    {
        return self::create([
            'user_type' => 'athlete',
            'athlete_id' => $athleteId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'deal_id' => $dealId,
            'message_id' => $messageId,
        ]);
    }
}
