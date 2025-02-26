<?php

namespace App\Libraries\Converters;

use Exception;
use Symfony\Component\Yaml\Yaml;

class DataFormatConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'application/json' => ['yaml', 'xml', 'csv'],
            'application/x-yaml' => ['json', 'xml'],
            'application/xml' => ['json', 'yaml', 'csv'],
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {
        if (!in_array($to, self::getSupportedConversions()[$from] ?? [])) {
            throw new Exception("Unsupported output format: $to");
        }

        $inputData = file_get_contents($filePath);
        
        switch ($from) {
            case 'application/json':
                $data = json_decode($inputData, true);
                break;
            case 'application/x-yaml':
                $data = Yaml::parse($inputData);
                break;
            case 'application/xml':
                $data = simplexml_load_string($inputData);
                $data = json_decode(json_encode($data), true);
                break;
            default:
                throw new Exception("Unsupported input format: $from");
        }
        
        switch ($to) {
            case 'json':
                file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT));
                break;
            case 'yaml':
                file_put_contents($outputPath, Yaml::dump($data, 2, 4));
                break;
            case 'xml':
                $xml = new \SimpleXMLElement('<root/>');
                $this->arrayToXml($data, $xml);
                file_put_contents($outputPath, $xml->asXML());
                break;
            case 'csv':
                $fp = fopen($outputPath, 'w');
                fputcsv($fp, array_keys(reset($data)));
                foreach ($data as $row) {
                    fputcsv($fp, $row);
                }
                fclose($fp);
                break;
            default:
                throw new Exception("Unsupported output format: $to");
        }
    }

    private function arrayToXml(array $data, \SimpleXMLElement &$xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml->addChild(is_numeric($key) ? 'item' : $key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xml->addChild(is_numeric($key) ? 'item' : $key, htmlspecialchars($value));
            }
        }
    }
}