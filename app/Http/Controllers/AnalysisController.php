<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
    use App\Models\SeoReport;


class AnalysisController extends Controller
{

public function analyze(Request $request)
{
    $report = SeoReport::create([
        'website_url' => $request->website_url,
        'project_name' => 'Test Project',

        'on_page_score' => 80,
        'technical_score' => 70,
        'off_page_score' => 50,
        'overall_score' => 67,

        'raw_seo_data' => [
            'meta_description' => null
        ],

        'page_speed_data' => [
            'performance' => 70
        ],

        'ai_fixes' => [
            'meta_title' => 'Demo Title'
        ],
    ]);

    return redirect()->route('report.show', $report);
}
}
