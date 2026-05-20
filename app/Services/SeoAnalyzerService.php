<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SeoAnalyzerService
{
    public function analyze(string $url): array
    {
        $issues = [];
        $scores = [];

        try {
            $response = Http::timeout(15)->get($url);
            $html = $response->body();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            // --- Title check ---
            $titles = $dom->getElementsByTagName('title');
            if ($titles->length === 0) {
                $issues[] = ['type' => 'missing_title', 'severity' => 'high',
                             'description' => 'Page is missing a <title> tag.'];
                $scores['title'] = 0;
            } else {
                $titleText = $titles->item(0)->textContent;
                $len = strlen($titleText);
                if ($len < 30 || $len > 60) {
                    $issues[] = ['type' => 'bad_title_length', 'severity' => 'medium',
                                 'description' => "Title length is {$len} chars. Ideal is 30–60."];
                    $scores['title'] = 50;
                } else {
                    $scores['title'] = 100;
                }
            }

            // --- Meta description check ---
            $metaDesc = $xpath->query('//meta[@name="description"]/@content');
            if ($metaDesc->length === 0 || empty($metaDesc->item(0)->nodeValue)) {
                $issues[] = ['type' => 'missing_meta_description', 'severity' => 'high',
                             'description' => 'Page is missing a meta description.'];
                $scores['meta'] = 0;
            } else {
                $metaLen = strlen($metaDesc->item(0)->nodeValue);
                if ($metaLen < 120 || $metaLen > 160) {
                    $issues[] = ['type' => 'bad_meta_length', 'severity' => 'medium',
                                 'description' => "Meta description is {$metaLen} chars. Ideal is 120–160."];
                    $scores['meta'] = 50;
                } else {
                    $scores['meta'] = 100;
                }
            }

            // --- H1 check ---
            $h1s = $dom->getElementsByTagName('h1');
            if ($h1s->length === 0) {
                $issues[] = ['type' => 'missing_h1', 'severity' => 'high',
                             'description' => 'Page has no H1 heading.'];
                $scores['h1'] = 0;
            } elseif ($h1s->length > 1) {
                $issues[] = ['type' => 'multiple_h1', 'severity' => 'medium',
                             'description' => "Page has {$h1s->length} H1 tags. Only one is recommended."];
                $scores['h1'] = 60;
            } else {
                $scores['h1'] = 100;
            }

            // --- Image alt check ---
            $images = $dom->getElementsByTagName('img');
            $missingAlt = 0;
            foreach ($images as $img) {
                if (!$img->hasAttribute('alt') || empty($img->getAttribute('alt'))) {
                    $missingAlt++;
                }
            }
            if ($missingAlt > 0) {
                $issues[] = ['type' => 'missing_alt_tags', 'severity' => 'medium',
                             'description' => "{$missingAlt} image(s) are missing alt attributes."];
                $scores['images'] = max(0, 100 - ($missingAlt * 10));
            } else {
                $scores['images'] = 100;
            }

            // --- Canonical check ---
            $canonical = $xpath->query('//link[@rel="canonical"]/@href');
            if ($canonical->length === 0) {
                $issues[] = ['type' => 'missing_canonical', 'severity' => 'low',
                             'description' => 'No canonical tag found.'];
                $scores['canonical'] = 50;
            } else {
                $scores['canonical'] = 100;
            }

        } catch (\Exception $e) {
            $issues[] = ['type' => 'fetch_error', 'severity' => 'high',
                         'description' => 'Could not fetch the URL: ' . $e->getMessage()];
        }

        $onPageScore = count($scores) > 0 ? (int) round(array_sum($scores) / count($scores)) : 0;

        return [
            'issues'       => $issues,
            'on_page_score' => $onPageScore,
            'raw_seo_data' => [
                'title_score'     => $scores['title']     ?? 0,
                'meta_score'      => $scores['meta']      ?? 0,
                'h1_score'        => $scores['h1']        ?? 0,
                'image_score'     => $scores['images']    ?? 0,
                'canonical_score' => $scores['canonical'] ?? 0,
            ],
        ];
    }
}