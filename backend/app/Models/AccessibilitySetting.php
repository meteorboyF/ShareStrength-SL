<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessibilitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'font_size',
        'tts_enabled',
        'stt_enabled',
        'high_contrast',
    ];

    protected $casts = [
        'tts_enabled' => 'boolean',
        'stt_enabled' => 'boolean',
        'high_contrast' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
