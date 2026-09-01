<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'locale', 'is_read', 'read_at', 'is_answered', 'answered_at', 'archived_at', 'internal_note'];

    protected $casts = ['is_read' => 'boolean', 'read_at' => 'datetime', 'is_answered' => 'boolean', 'answered_at' => 'datetime', 'archived_at' => 'datetime'];

    public function markRead(): void
    {
        $this->forceFill(['is_read' => true, 'read_at' => $this->read_at ?? now()])->save();
    }

    public function markAnswered(): void
    {
        $this->forceFill(['is_answered' => true, 'answered_at' => $this->answered_at ?? now()])->save();
    }

    public function archive(): void
    {
        $this->forceFill(['archived_at' => now()])->save();
    }
}
