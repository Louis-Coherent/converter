<?php

namespace App\Libraries\Converters;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\Word2007;
use PhpOffice\PhpWord\Writer\RTF;
use PhpOffice\PhpWord\Writer\ODText;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

class WordConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'application/msword' => ['docx', 'pdf', 'rtf', 'odt', 'txt', 'html', 'epub'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['pdf', 'rtf', 'odt', 'txt', 'html', 'epub', 'doc'],
            'application/rtf' => ['docx', 'pdf', 'odt', 'txt', 'html', 'epub'],
            'application/vnd.oasis.opendocument.text' => ['docx', 'pdf', 'rtf', 'txt', 'html', 'epub'],
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {
        // Check if the conversion is supported
        if (!in_array($to, self::getSupportedConversions()[$from])) {
            throw new Exception("Unsupported output format: $to");
        }

        // Load the Word document
        $phpWord = IOFactory::load($filePath);

        // Select the appropriate writer based on the desired output format
        switch ($to) {
            case 'docx':
                $writer = new Word2007($phpWord);
                break;
            case 'pdf':
                $this->convertToPdf($phpWord, $outputPath);
                return;
            case 'rtf':
                $writer = new RTF($phpWord);
                break;
            case 'odt':
                $writer = new ODText($phpWord);
                break;
            case 'txt':
                $this->convertToTxt($phpWord, $outputPath);
                return;
            case 'html':
                $this->convertToHtml($phpWord, $outputPath);
                return;
            case 'epub':
                $this->convertToEpub($phpWord, $outputPath);
                return;
            default:
                throw new Exception("Unsupported conversion type: $to");
        }

        // Save the converted file
        $writer->save($outputPath);
    }

    private function convertToPdf($phpWord, string $outputPath)
    {
        // Initialize Dompdf for PDF conversion
        $options = new Options();
        $options->set("isHtml5ParserEnabled", true);
        $options->set("isPhpEnabled", true);
        $dompdf = new Dompdf($options);

        // Save Word content as HTML, which can be passed to Dompdf
        $html = $this->convertToHtml($phpWord);

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Output the PDF file to the specified output path
        file_put_contents($outputPath, $dompdf->output());
    }

    private function convertToTxt($phpWord, string $outputPath)
    {
        // Extract the text content from the Word document
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        // Save the extracted text to a .txt file
        file_put_contents($outputPath, $text);
    }

    private function convertToHtml($phpWord, string $outputPath = null)
    {
        // Convert the Word document to HTML
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

        // If no output path is provided, return the HTML as a string
        if ($outputPath === null) {
            ob_start();
            $htmlWriter->save("php://output");
            $htmlContent = ob_get_clean();
            return $htmlContent;
        }

        // Otherwise, save the HTML to the file
        $htmlWriter->save($outputPath);
    }

    private function convertToEpub($phpWord, string $outputPath)
    {
        // Convert Word document to HTML first
        $htmlContent = $this->convertToHtml($phpWord);

        // Convert the HTML to EPUB format (external libraries may be needed for better EPUB support)
        // Use something like `phpepub` or external conversion tools.

        // In this example, let's just save the HTML to an EPUB file (you could integrate a full EPUB library)
        $epubContent = "<html>" . $htmlContent . "</html>";

        // Save to EPUB
        file_put_contents($outputPath, $epubContent);
    }
}
