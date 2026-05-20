<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'raw_seo_data' => 'array',
        'page_speed_data' => 'array',
        'ai_fixes' => 'array',
    ];

    public function issues()
    {
        return $this->hasMany(SeoIssue::class);
    }

    public function domains()
    {
        return $this->hasMany(DomainRecommendation::class);
    }

    public function hosting()
    {
        return $this->hasMany(HostingRecommendation::class);
    }
}
