<?php

namespace App\Services;

class AiFixGeneratorService
{
    public function __construct(protected GeminiService $gemini) {}

    public function generate(array $issues, string $url): array
    {
        if (empty($issues)) {
            return ['summary' => 'No major SEO issues found!', 'fixes' => []];
        }

        $issueList = collect($issues)
            ->map(fn($i) => "- [{$i['severity']}] {$i['description']}")
            ->join("\n");

        $prompt = <<<PROMPT
You are an expert SEO consultant. Analyze these SEO issues found on "{$url}" and provide specific, actionable fixes.

Issues found:
{$issueList}

Respond ONLY in valid JSON format like this (no markdown, no backticks):
{
  "summary": "brief overall assessment",
  "fixes": [
    {
      "issue": "issue type",
      "fix": "specific action to take",
      "priority": "high|medium|low",
      "example": "concrete example if applicable"
    }
  ],
  "optimized_title": "suggested SEO title for the page",
  "optimized_meta": "suggested meta description 120-160 chars"
}
PROMPT;

        $raw = $this->gemini->ask($prompt);

        // Strip markdown code fences if present
        $clean = preg_replace('/```json|```/', '', $raw);

        try {
            return json_decode(trim($clean), true) ?? ['summary' => $raw, 'fixes' => []];
        } catch (\Exception $e) {
            return ['summary' => $raw, 'fixes' => []];
        }
    }
}