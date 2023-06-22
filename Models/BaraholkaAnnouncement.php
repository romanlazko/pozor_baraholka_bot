<?php

namespace App\Bots\pozor_baraholka_bot\Models;

use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\Announcement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Romanlazko\Telegram\Models\TelegramChat;

class BaraholkaAnnouncement extends Model
{
    use HasFactory; use SoftDeletes;

    protected $guarded = [];

    public function photos()
    {
        return $this->hasMany(BaraholkaAnnouncementPhoto::class, 'announcement_id', 'id');
    }

    public function chat()
    {
        return $this->belongsTo(TelegramChat::class, 'chat', 'id');
    }

    public function dto()
    {
        return Announcement::fromObject($this);
    }

    public function prepare()
    {
        return Announcement::fromObject($this)->prepare();
    }

    public function getPhotoAttribute()
    {
        return Announcement::fromObject($this)->photos;
    }
}
