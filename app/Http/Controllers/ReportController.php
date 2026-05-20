<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoReport;
use App\Models\ChatMessage;
use App\Services\GeminiService;

class ReportController extends Controller
{
    public function __construct(protected GeminiService $gemini) {}

    public function show(SeoReport $report)
    {
        $chatHistory = $report->chatMessages()->orderBy('created_at')->get();
        return view('pages.report', compact('report', 'chatHistory'));
    }

    /**
     * Handle an AI chat message scoped to the current report context.
     */
    public function chat(Request $request, SeoReport $report)
    {
        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $userMessage = $request->message;

        // Build a rich context prompt from the report data
        $context = $this->buildContext($report);

        $prompt = <<<PROMPT
You are an expert SEO consultant reviewing the analysis for {$report->website_url}.

=== SITE ANALYSIS CONTEXT ===
{$context}

=== USER QUESTION ===
{$userMessage}

Answer concisely and practically. If suggesting code, wrap it in ```html, ```php, or appropriate code blocks.
Focus on actionable steps the developer can implement immediately.
PROMPT;

        $aiResponse = $this->gemini->ask($prompt);
        // Persist both sides of the conversation
        $report->chatMessages()->createMany([
            ['role' => 'user',      'message' => $userMessage],
            ['role' => 'assistant', 'message' => $aiResponse],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'user'      => $userMessage,
                'assistant' => $aiResponse,
            ]);
        }

        return back();
    }

    private function buildContext(SeoReport $report): string
    {
        $raw    = $report->raw_seo_data ?? [];
        $ps     = $report->page_speed_data ?? [];
        $fixes  = $report->ai_fixes ?? [];
        $issues = $raw['issues'] ?? [];

        $issueLines = collect($issues)
            ->map(fn($i) => "  - [{$i['severity']}] {$i['description']}")
            ->join("\n");

        $fixSummary = $fixes['summary'] ?? 'No summary available.';

        return <<<CTX
URL: {$report->website_url}
Project: {$report->project_name}

SCORES:
  - On-Page SEO:  {$report->on_page_score}/100
  - Technical:    {$report->technical_score}/100
  - Off-Page:     {$report->off_page_score}/100
  - Overall:      {$report->overall_score}/100

ON-PAGE ISSUES FOUND:
{$issueLines}

TECHNICAL (PageSpeed):
  - Performance:       {$ps['performance']} / 100
  - SEO:               {$ps['seo']} / 100
  - Accessibility:     {$ps['accessibility']} / 100
  - FCP:               {$ps['first_contentful_paint']}
  - LCP:               {$ps['largest_contentful_paint']}
  - TBT:               {$ps['total_blocking_time']}
  - CLS:               {$ps['cumulative_layout_shift']}
  - Speed Index:       {$ps['speed_index']}

AI FIX SUMMARY:
{$fixSummary}
CTX;
    }
}