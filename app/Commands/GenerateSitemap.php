<?php

namespace App\Commands;

use CodeIgniter\CLI\CLI;
use Config\FileConversion;
use CodeIgniter\CLI\BaseCommand;
use Symfony\Component\Mime\MimeTypes;


class GenerateSitemap extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'sitemap:generate';
    protected $description = 'Generates a dynamic XML sitemap for the website, including conversion routes';

    public function run(array $params = [])
    {
        $sitemapPath = substr(__DIR__, 0, strpos(__DIR__, 'app')) . 'public/sitemaps';

        // Ensure the directory exists
        if (!is_dir($sitemapPath)) {
            mkdir($sitemapPath, 0777, true);
        }

        $sitemapPath .= '/sitemap.xml';

        // Create a new XMLWriter instance
        $xml = new \XMLWriter();
        $xml->openURI($sitemapPath);
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $siteUrl = 'https://file-shift.com/';
        // Add the homepage or root URL
        $xml->startElement('url');
        $xml->writeElement('loc', $siteUrl);
        $xml->writeElement('lastmod', date('Y-m-d'));
        $xml->writeElement('priority', '1.0');
        $xml->endElement(); // url

        $xml->startElement('url');
        $xml->writeElement('loc', $siteUrl . 'supported-files');
        $xml->writeElement('lastmod', date('Y-m-d'));
        $xml->writeElement('priority', '1.0');
        $xml->endElement(); // url

        // Get the mimeTypes from your FileConversion class
        $mimeTypes = FileConversion::mimeTypes;

        // Loop through mimeTypes and extensions and add to the sitemap
        foreach ($mimeTypes as $mimeType => $extensions) {
            foreach ($extensions as $extension) {

                $mimeTypesChange = new MimeTypes();

                $newMimeType = $mimeTypesChange->getExtensions($mimeType)[0] ?? '';

                // Construct the URL for the conversion route
                $url = $siteUrl . ("{$newMimeType}-to-{$extension}");

                // Add a URL entry for this conversion route
                $xml->startElement('url');
                $xml->writeElement('loc', $url);
                $xml->writeElement('lastmod', date('Y-m-d'));
                $xml->writeElement('priority', '0.8');
                $xml->endElement(); // url
            }
        }

        // Close the XML structure
        $xml->endElement(); // urlset
        $xml->endDocument();

        // Close the file and output success message
        $xml->flush();

        CLI::write("Sitemap generated successfully: " . $sitemapPath, 'green');
    }
}
