<?php

namespace App\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\BaseCommand;

class TestSitemap extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'sitemap:test';
    protected $description = 'Checks all URLs in the sitemap to ensure they are reachable (2xx or 3xx).';

    public function run(array $params = [])
    {
        $sitemapPath = substr(__DIR__, 0, strpos(__DIR__, 'app')) . 'public/sitemap.xml';

        if (!file_exists($sitemapPath)) {
            CLI::error("Sitemap file not found: " . $sitemapPath);
            return;
        }

        $xml = simplexml_load_file($sitemapPath);
        if (!$xml) {
            CLI::error("Failed to parse sitemap.xml");
            return;
        }

        $failedUrls = [];
        foreach ($xml->url as $url) {
            $loc = (string) $url->loc;
            $status = $this->getHttpStatus($loc);

            if ($status >= 200 && $status < 400) {
                CLI::write("[OK] $loc ($status)", 'green');
            } else {
                CLI::write("[FAIL] $loc ($status)", 'red');
                $failedUrls[] = "$loc ($status)";
            }
        }

        if (!empty($failedUrls)) {
            CLI::newLine();
            CLI::error("Some URLs failed:");
            foreach ($failedUrls as $failed) {
                CLI::write($failed, 'red');
            }
        } else {
            CLI::newLine();
            CLI::write("All URLs passed.", 'green');
        }
    }

    private function getHttpStatus(string $url): int
    {
        $headers = @get_headers($url);
        if (!$headers || !isset($headers[0])) {
            return 0;
        }
        preg_match('/\d{3}/', $headers[0], $matches);
        return isset($matches[0]) ? (int) $matches[0] : 0;
    }
}
