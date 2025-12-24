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
    ];

    protected function casts(): array
    {
        return [
            'is_system_message' => 'boolean',
            'attachment_size' => 'integer',
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
}
