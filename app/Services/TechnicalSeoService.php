<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TechnicalSeoService
{
    public function check(string $url): array
    {
        try {
            $apiKey = config('services.pagespeed.key');
            $response = Http::timeout(30)->get(
                'https://www.googleapis.com/pagespeedonline/v5/runPagespeed',
                [
                    'url'      => $url,
                    'key'      => $apiKey,
                    'strategy' => 'mobile',
                ]
            );

            $data = $response->json();
            $categories  = $data['lighthouseResult']['categories']  ?? [];
            $audits      = $data['lighthouseResult']['audits']       ?? [];

            $perfScore  = isset($categories['performance']['score'])
                            ? (int) round($categories['performance']['score'] * 100) : 0;
            $seoScore   = isset($categories['seo']['score'])
                            ? (int) round($categories['seo']['score'] * 100) : 0;
            $a11yScore  = isset($categories['accessibility']['score'])
                            ? (int) round($categories['accessibility']['score'] * 100) : 0;

            return [
                'technical_score' => $perfScore,
                'page_speed_data' => [
                    'performance'          => $perfScore,
                    'seo'                  => $seoScore,
                    'accessibility'        => $a11yScore,
                    'first_contentful_paint' => $audits['first-contentful-paint']['displayValue'] ?? 'N/A',
                    'largest_contentful_paint' => $audits['largest-contentful-paint']['displayValue'] ?? 'N/A',
                    'total_blocking_time'  => $audits['total-blocking-time']['displayValue'] ?? 'N/A',
                    'cumulative_layout_shift' => $audits['cumulative-layout-shift']['displayValue'] ?? 'N/A',
                    'speed_index'          => $audits['speed-index']['displayValue'] ?? 'N/A',
                ],
            ];

        } catch (\Exception $e) {
            return [
                'technical_score' => 0,
                'page_speed_data' => ['error' => $e->getMessage()],
            ];
        }
    }
}