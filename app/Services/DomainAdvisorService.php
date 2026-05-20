<?php

namespace App\Services;

class DomainAdvisorService
{
    public function __construct(protected GeminiService $gemini) {}

    public function suggest(string $projectName, string $url): array
    {
        $prompt = <<<PROMPT
You are a domain name expert. Suggest 6 creative, memorable domain names for a project called "{$projectName}" (website: {$url}).

Respond ONLY in valid JSON (no markdown, no backticks):
{
  "domains": [
    {
      "name": "example.com",
      "reason": "why this domain is good",
      "type": "exact|brandable|keyword"
    }
  ]
}
PROMPT;

        $raw   = $this->gemini->ask($prompt);
        $clean = preg_replace('/```json|```/', '', $raw);

        try {
            $data = json_decode(trim($clean), true);
            return $data['domains'] ?? $this->fallback($projectName);
        } catch (\Exception $e) {
            return $this->fallback($projectName);
        }
    }

    private function fallback(string $name): array
    {
        $slug = strtolower(preg_replace('/\s+/', '', $name));
        return [
            ['name' => "{$slug}.com",    'reason' => 'Clean exact match',  'type' => 'exact'],
            ['name' => "{$slug}app.com", 'reason' => 'App-style branding', 'type' => 'brandable'],
            ['name' => "get{$slug}.com", 'reason' => 'Action-oriented',    'type' => 'keyword'],
        ];
    }
}