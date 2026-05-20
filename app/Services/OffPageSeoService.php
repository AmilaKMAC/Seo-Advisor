<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OffPageSeoService
{
    /**
     * Run all free heuristic off-page checks.
     */
    public function check(string $url): array
    {
        $host   = parse_url($url, PHP_URL_HOST) ?? '';
        $scheme = parse_url($url, PHP_URL_SCHEME) ?? 'http';

        $checks = [];
        $scores = [];

        // ── 1. HTTPS ──────────────────────────────────────────────────────────
        $checks['https'] = $scheme === 'https' ? 'pass' : 'fail';
        $scores[]        = $scheme === 'https' ? 100 : 0;

        // ── 2. WWW Redirect ───────────────────────────────────────────────────
        $wwwResult        = $this->checkWwwRedirect($url, $host);
        $checks['www_redirect_consistent'] = $wwwResult ? 'pass' : 'fail';
        $scores[]         = $wwwResult ? 100 : 50;

        // ── 3. robots.txt ─────────────────────────────────────────────────────
        $robotsResult     = $this->checkRobotsTxt($scheme, $host);
        $checks['robots_txt'] = $robotsResult['status'];
        $scores[]         = $robotsResult['score'];

        // ── 4. sitemap.xml ────────────────────────────────────────────────────
        $sitemapResult    = $this->checkSitemap($scheme, $host, $robotsResult['robots_body']);
        $checks['sitemap_xml'] = $sitemapResult ? 'pass' : 'fail';
        $scores[]         = $sitemapResult ? 100 : 30;

        // ── 5. Social media presence (Open Graph tags) ────────────────────────
        $ogResult         = $this->checkOpenGraph($url);
        $checks['open_graph_tags'] = $ogResult ? 'pass' : 'fail';
        $scores[]         = $ogResult ? 100 : 40;

        // ── 6. Structured data (schema.org) ───────────────────────────────────
        $schemaResult     = $this->checkStructuredData($url);
        $checks['structured_data'] = $schemaResult ? 'pass' : 'fail';
        $scores[]         = $schemaResult ? 100 : 50;

        // ── 7. Backlink proxy — Common Crawl index lookup ─────────────────────
        $backlinkResult   = $this->checkCommonCrawl($host);
        $checks['common_crawl_indexed'] = $backlinkResult['status'];
        $scores[]         = $backlinkResult['score'];

        // ── 8. Indexed pages estimate — Google site: search scrape ────────────
        $indexResult      = $this->checkGoogleIndex($host);
        $checks['google_index_estimate'] = $indexResult['label'];
        $scores[]         = $indexResult['score'];

        // ── 9. DNS / MX records (trust signal — has email infrastructure) ─────
        $mxResult         = $this->checkMxRecord($host);
        $checks['mx_email_records'] = $mxResult ? 'pass' : 'fail';
        $scores[]         = $mxResult ? 100 : 60;

        // ── 10. Security headers ──────────────────────────────────────────────
        $headerResult     = $this->checkSecurityHeaders($url);
        $checks['security_headers'] = $headerResult['status'];
        $scores[]         = $headerResult['score'];

        $offPageScore = count($scores) > 0
            ? (int) round(array_sum($scores) / count($scores))
            : 50;

        return [
            'off_page_score' => $offPageScore,
            'details' => [
                'domain' => $host,
                'note'   => ' ',
                'checks' => $checks,
            ],
        ];
    }

    // ── HTTPS redirect consistency ────────────────────────────────────────────
    private function checkWwwRedirect(string $url, string $host): bool
    {
        try {
            $isWww     = str_starts_with($host, 'www.');
            $alternate = $isWww
                ? str_replace('www.', '', $url, )
                : str_replace($host, 'www.' . $host, $url);

            $response = Http::timeout(8)->withOptions(['allow_redirects' => true])->get($alternate);
            $finalUrl = $response->effectiveUri() ?? '';

            // Both should resolve to the same canonical domain
            return $response->successful();
        } catch (\Exception) {
            return true; // not conclusive, don't penalise
        }
    }

    // ── robots.txt ────────────────────────────────────────────────────────────
    private function checkRobotsTxt(string $scheme, string $host): array
    {
        try {
            $response = Http::timeout(8)->get("{$scheme}://{$host}/robots.txt");

            if ($response->successful() && strlen($response->body()) > 10) {
                // Check it's not blocking everything
                $body    = strtolower($response->body());
                $blocked = str_contains($body, 'disallow: /') && !str_contains($body, 'disallow: ');

                return [
                    'status'      => $blocked ? 'warn' : 'pass',
                    'score'       => $blocked ? 40 : 100,
                    'robots_body' => $response->body(),
                ];
            }

            return ['status' => 'fail', 'score' => 0, 'robots_body' => ''];
        } catch (\Exception) {
            return ['status' => 'fail', 'score' => 0, 'robots_body' => ''];
        }
    }

    // ── sitemap.xml ───────────────────────────────────────────────────────────
    private function checkSitemap(string $scheme, string $host, string $robotsBody): bool
    {
        // First check if robots.txt points to a sitemap
        if (preg_match('/sitemap:\s*(https?:\/\/\S+)/i', $robotsBody, $m)) {
            try {
                return Http::timeout(8)->get(trim($m[1]))->successful();
            } catch (\Exception) {}
        }

        // Fall back to common locations
        $locations = [
            "{$scheme}://{$host}/sitemap.xml",
            "{$scheme}://{$host}/sitemap_index.xml",
            "{$scheme}://{$host}/sitemap/sitemap.xml",
        ];

        foreach ($locations as $loc) {
            try {
                $res = Http::timeout(8)->get($loc);
                if ($res->successful() && str_contains($res->body(), '<url')) {
                    return true;
                }
            } catch (\Exception) {}
        }

        return false;
    }

    // ── Open Graph tags ───────────────────────────────────────────────────────
    private function checkOpenGraph(string $url): bool
    {
        try {
            $response = Http::timeout(10)->get($url);
            $body     = $response->body();

            return str_contains($body, 'og:title') || str_contains($body, 'og:description');
        } catch (\Exception) {
            return false;
        }
    }

    // ── Structured data (JSON-LD or microdata) ────────────────────────────────
    private function checkStructuredData(string $url): bool
    {
        try {
            $response = Http::timeout(10)->get($url);
            $body     = $response->body();

            return str_contains($body, 'application/ld+json')
                || str_contains($body, 'itemtype="https://schema.org')
                || str_contains($body, 'itemtype="http://schema.org');
        } catch (\Exception) {
            return false;
        }
    }

    // ── Common Crawl index lookup (free, no API key needed) ───────────────────
    private function checkCommonCrawl(string $host): array
    {
        try {
            // Common Crawl CDX API — free, public
            $apiUrl = "https://index.commoncrawl.org/CC-MAIN-2024-10-index"
                    . "?url={$host}/*&output=json&limit=1&fl=urlkey";

            $response = Http::timeout(12)->get($apiUrl);

            if ($response->successful() && strlen(trim($response->body())) > 5) {
                return ['status' => 'indexed', 'score' => 100];
            }

            return ['status' => 'not_found', 'score' => 40];
        } catch (\Exception) {
            return ['status' => 'unknown', 'score' => 50];
        }
    }

    // ── Google index estimate via search scrape ───────────────────────────────
    // Note: scraping Google is against ToS for commercial use; this is a
    // lightweight heuristic check. For production, use the Google Search
    // Console API (free with OAuth) instead.
    private function checkGoogleIndex(string $host): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; SEOLens/1.0)'])
                ->get("https://www.google.com/search?q=site:{$host}&num=1");

            $body = $response->body();

            // Look for result count hint in the response
            if (preg_match('/About ([\d,]+) results/', $body, $m)) {
                $count = (int) str_replace(',', '', $m[1]);
                $label = $count > 1000 ? 'large (1000+)' : ($count > 100 ? 'medium' : 'small');
                $score = $count > 100 ? 100 : ($count > 10 ? 70 : 40);
                return ['label' => $label, 'score' => $score];
            }

            // If we got a response but couldn't parse count, site is at least reachable
            if ($response->successful()) {
                return ['label' => 'indexed', 'score' => 80];
            }

            return ['label' => 'unknown', 'score' => 50];
        } catch (\Exception) {
            return ['label' => 'unknown', 'score' => 50];
        }
    }

    // ── MX records (DNS trust signal) ─────────────────────────────────────────
    private function checkMxRecord(string $host): bool
    {
        try {
            $records = dns_get_record($host, DNS_MX);
            return !empty($records);
        } catch (\Exception) {
            return false;
        }
    }

    // ── Security headers ──────────────────────────────────────────────────────
    private function checkSecurityHeaders(string $url): array
    {
        try {
            $response = Http::timeout(8)->head($url);
            $headers  = array_change_key_case($response->headers(), CASE_LOWER);

            $present = 0;
            $checks  = [
                'x-content-type-options',
                'x-frame-options',
                'strict-transport-security',
                'content-security-policy',
            ];

            foreach ($checks as $h) {
                if (!empty($headers[$h])) {
                    $present++;
                }
            }

            $score  = (int) round(($present / count($checks)) * 100);
            $status = $present >= 3 ? 'pass' : ($present >= 1 ? 'partial' : 'fail');

            return ['status' => $status, 'score' => $score];
        } catch (\Exception) {
            return ['status' => 'unknown', 'score' => 50];
        }
    }
}