<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoReport extends Model
{
    protected $fillable = [
        'project_name',
        'website_url',
        'on_page_score',
        'technical_score',
        'off_page_score',
        'overall_score',
        'raw_seo_data',
        'page_speed_data',
        'ai_fixes',
    ];

    protected $casts = [
        'raw_seo_data'    => 'array',
        'page_speed_data' => 'array',
        'ai_fixes'        => 'array',
    ];

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function getScoreColorAttribute(): string
    {
        return match(true) {
            $this->overall_score >= 80 => 'green',
            $this->overall_score >= 50 => 'amber',
            default                    => 'red',
        };
    }
}