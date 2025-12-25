<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'sender_type',
        'sender_id',
        'athlete_sender_id',
        'message_type',
        'content',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size',
        'is_system_message',
        'read_by_user_ids',
        'read_by_athlete_ids',
    ];

    protected function casts(): array
    {
        return [
            'is_system_message' => 'boolean',
            'attachment_size' => 'integer',
            'read_by_user_ids' => 'array',
            'read_by_athlete_ids' => 'array',
        ];
    }

    /**
     * Get the deal this message belongs to
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * Get the user sender (SMB)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the athlete sender
     */
    public function athleteSender(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_sender_id');
    }

    /**
     * Get the sender's display name
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->is_system_message) {
            return 'System';
        }

        if ($this->sender_type === 'athlete' && $this->athleteSender) {
            return $this->athleteSender->name ?? 'Athlete';
        }

        if ($this->sender_type === 'user' && $this->sender) {
            return $this->sender->business_name ?? $this->sender->name ?? 'Business';
        }

        return 'Unknown';
    }

    /**
     * Check if message has attachment
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return \Storage::url($this->attachment_path);
    }

    /**
     * Create a system message
     */
    public static function createSystemMessage(int $dealId, string $content): self
    {
        return self::create([
            'deal_id' => $dealId,
            'sender_type' => 'system',
            'message_type' => 'system',
            'content' => $content,
            'is_system_message' => true,
        ]);
    }

    /**
     * Mark message as read by a user (SMB)
     */
    public function markAsReadByUser(int $userId): void
    {
        $readBy = $this->read_by_user_ids ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->read_by_user_ids = $readBy;
            $this->save();
        }
    }

    /**
     * Mark message as read by an athlete
     */
    public function markAsReadByAthlete(int $athleteId): void
    {
        $readBy = $this->read_by_athlete_ids ?? [];
        if (!in_array($athleteId, $readBy)) {
            $readBy[] = $athleteId;
            $this->read_by_athlete_ids = $readBy;
            $this->save();
        }
    }

    /**
     * Check if message is read by a user (SMB)
     */
    public function isReadByUser(int $userId): bool
    {
        $readBy = $this->read_by_user_ids ?? [];
        return in_array($userId, $readBy);
    }

    /**
     * Check if message is read by an athlete
     */
    public function isReadByAthlete(int $athleteId): bool
    {
        $readBy = $this->read_by_athlete_ids ?? [];
        return in_array($athleteId, $readBy);
    }
}
