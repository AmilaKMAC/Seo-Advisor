<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = ['seo_report_id', 'role', 'message'];

    public function seoReport(): BelongsTo
    {
        return $this->belongsTo(SeoReport::class);
    }
}