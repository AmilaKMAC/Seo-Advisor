<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey   = config('services.gemini.key');
        $this->endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    }

    public function ask(string $prompt): string
    {
        try {
            $response = Http::timeout(30)->post(
                $this->endpoint . '?key=' . $this->apiKey,
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]
            );

            return $response->json('candidates.0.content.parts.0.text') ?? 'No response from AI.';

        } catch (\Exception $e) {
            return 'AI service error: ' . $e->getMessage();
        }
    }
}
