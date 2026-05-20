<?php

namespace App\Http\Controllers;

use App\Models\SeoReport;
use App\Services\AiFixGeneratorService;
use App\Services\SeoAnalyzerService;
use App\Services\TechnicalSeoService;
use App\Services\OffPageSeoService;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct(
        protected SeoAnalyzerService $seoAnalyzer,
        protected TechnicalSeoService $technicalSeo,
        protected AiFixGeneratorService $aiFixGenerator,
        protected OffPageSeoService $offPageSeo,
    ) {}

    public function analyze(Request $request)
    {
        $request->validate([
            'website_url' => ['required', 'url', 'max:2048'],
            'project_name' => ['nullable', 'string', 'max:255'],
        ]);

        $url = rtrim($request->website_url, '/');
        $projectName = $request->project_name ?? parse_url($url, PHP_URL_HOST) ?? 'Untitled Project';

        // ── Pillar 1: On-Page SEO ─────────────────────────────────────────────
        $onPage = $this->seoAnalyzer->analyze($url);

        // ── Pillar 2: Technical SEO (PageSpeed) ───────────────────────────────
        $technical = $this->technicalSeo->check($url);

        // ── Pillar 3: Off-Page (placeholder — backlink APIs are paid) ─────────
        $offPage = $this->offPageSeo->check($url); 

        // ── AI Suggestions (based on on-page issues) ──────────────────────────
        $aiFixes = $this->aiFixGenerator->generate($onPage['issues'], $url);

        // ── Overall score ─────────────────────────────────────────────────────
        $overallScore = (int) round(
            ($onPage['on_page_score'] + $technical['technical_score'] + $offPage['off_page_score']) / 3
        );

        // ── Persist ───────────────────────────────────────────────────────────
        $report = SeoReport::create([
            'project_name' => $projectName,
            'website_url' => $url,
            'on_page_score' => $onPage['on_page_score'],
            'technical_score' => $technical['technical_score'],
            'off_page_score' => $offPage['off_page_score'],
            'overall_score' => $overallScore,
            'raw_seo_data' => array_merge($onPage['raw_seo_data'], [
                'issues' => $onPage['issues'],
                'off_page' => $offPage['details'],
            ]),
            'page_speed_data' => $technical['page_speed_data'],
            'ai_fixes' => $aiFixes,
        ]);

        return redirect()->route('report.show', $report);
    }

    // ── Off-page: basic heuristic checks (no paid API needed) ─────────────────
    private function buildOffPageData(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $score = 50; // neutral baseline — real off-page needs Ahrefs / Moz API
        $details = [
            'note' => ' ',
            'domain' => $host,
            'checks' => [
                'https' => str_starts_with($url, 'https') ? 'pass' : 'fail',
                'domain_age' => 'unknown',
                'backlinks' => 'not checked',
                'social_signals' => 'not checked',
            ],
        ];

        // Bump score if HTTPS
        if (str_starts_with($url, 'https')) {
            $score = 60;
        }

        return [
            'off_page_score' => $score,
            'details' => $details,
        ];
    }
}
