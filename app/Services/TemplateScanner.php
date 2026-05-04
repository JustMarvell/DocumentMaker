<?php

namespace App\Services;

use ZipArchive;

class TemplateScanner
{
    /**
     * Extract {{ variable }} placeholders from a .docx or .xlsx template.
     * Returns array of unique variable names (excluding Jinja2 keywords).
     */
    public function scan(string $templateFilename): array
    {
        $path = base_path('document_templates/' . $templateFilename);
        if (!file_exists($path))
            return [];

        $ext = strtolower(pathinfo($templateFilename, PATHINFO_EXTENSION));

        $xmlContents = match ($ext) {
            'docx' => $this->extractDocxXml($path),
            'xlsx' => $this->extractXlsxXml($path),
            default => [],
        };

        $variables = [];
        $jinja2Keywords = ['for', 'endfor', 'if', 'endif', 'else', 'elif', 'loop', 'true', 'false', 'none'];

        foreach ($xmlContents as $xml) {
            // Match {{ variable_name }} and {{ variable.property }}
            preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_.]*)\s*(?:\|[^}]*)?\}\}/', $xml, $matches);
            foreach ($matches[1] as $var) {
                $root = explode('.', $var)[0]; // get root: peserta.name -> peserta
                if (!in_array(strtolower($root), $jinja2Keywords)) {
                    $variables[] = $root;
                }
            }
        }

        return array_values(array_unique($variables));
    }

    private function extractDocxXml(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true)
            return [];

        $targets = [
            'word/document.xml',
            'word/header1.xml',
            'word/footer1.xml',
            'word/header2.xml',
            'word/footer2.xml'
        ];
        $contents = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (in_array($name, $targets) || (str_starts_with($name, 'word/') && str_ends_with($name, '.xml'))) {
                $contents[] = $zip->getFromIndex($i);
            }
        }

        $zip->close();

        return array_map(fn($xml) => $this->mergeDocxRuns($xml), $contents);
    }

    private function extractXlsxXml(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true)
            return [];

        $contents = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (
                str_starts_with($name, 'xl/worksheets/') || $name === 'xl/sharedStrings.xml'
                || str_starts_with($name, 'xl/drawings/')
            ) {
                $contents[] = $zip->getFromIndex($i);
            }
        }

        $zip->close();
        return $contents;
    }

    private function mergeDocxRuns(string $xml): string
    {
        return preg_replace('/<[^>]+>/', ' ', $xml);
    }
}