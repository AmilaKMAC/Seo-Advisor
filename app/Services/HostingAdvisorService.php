<?php

namespace App\Services;

class HostingAdvisorService
{
    public function __construct(protected GeminiService $gemini) {}

    public function recommend(string $projectName, string $url): array
    {
        $prompt = <<<PROMPT
You are a hosting expert. Recommend the best 4 hosting platforms for a web project called "{$projectName}".
Consider it may be built with Laravel/PHP.

Respond ONLY in valid JSON (no markdown, no backticks):
{
  "recommendations": [
    {
      "name": "Platform name",
      "reason": "why it suits this project",
      "pricing": "free|cheap|mid|enterprise",
      "best_for": "what type of projects"
    }
  ]
}
PROMPT;

        $raw   = $this->gemini->ask($prompt);
        $clean = preg_replace('/```json|```/', '', $raw);

        try {
            $data = json_decode(trim($clean), true);
            return $data['recommendations'] ?? $this->fallback();
        } catch (\Exception $e) {
            return $this->fallback();
        }
    }

    private function fallback(): array
    {
        return [
            ['name' => 'Laravel Forge + DigitalOcean', 'reason' => 'Best for Laravel',    'pricing' => 'mid',   'best_for' => 'PHP/Laravel apps'],
            ['name' => 'Railway',                       'reason' => 'Easy deployment',      'pricing' => 'free',  'best_for' => 'Small to medium apps'],
            ['name' => 'Render',                        'reason' => 'Free tier available',  'pricing' => 'free',  'best_for' => 'Startups'],
            ['name' => 'Hostinger',                     'reason' => 'Affordable shared',    'pricing' => 'cheap', 'best_for' => 'Budget projects'],
        ];
    }
}