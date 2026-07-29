<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    /**
     * Extrai apenas dígitos numéricos de uma imagem enviada usando Tesseract OCR.
     */
    public function readSerial(UploadedFile $image): string
    {
        $path = $image->getRealPath();

        $ocr = new TesseractOCR($path);

        $tesseractPath = (string) config('services.tesseract.path');
        if (! empty($tesseractPath)) {
            $ocr->executable($tesseractPath);
        }

        // Configura o Tesseract para reconhecer exclusivamente números (0-9)
        $ocr->allowlist(range(0, 9));

        $rawText = (string) $ocr->run();

        return $this->extractCleanSerial($rawText);
    }

    /**
     * Extrai o número de série/patrimônio ignorando ruídos gerados por código de barras.
     */
    public function extractCleanSerial(string $rawText): string
    {
        $lines = preg_split('/[\r\n]+/', $rawText) ?: [];
        $candidates = [];

        foreach ($lines as $line) {
            if (preg_match_all('/\d+/', $line, $matches)) {
                foreach ($matches[0] as $match) {
                    // Descarta ruído de código de barras (4 ou mais '1's repetidos como 111111)
                    if (preg_match('/1{4,}/', $match)) {
                        continue;
                    }
                    // Descarta dígitos idênticos repetidos 4+ vezes (ex: 0000, 1111)
                    if (preg_match('/(\d)\1{3,}/', $match)) {
                        continue;
                    }
                    // Números válidos de serial/patrimônio (3 a 15 dígitos)
                    if (strlen($match) >= 3 && strlen($match) <= 15) {
                        $candidates[] = $match;
                    }
                }
            }
        }

        if (! empty($candidates)) {
            // Prioriza candidatos com tamanho típico de serial/patrimônio (4 a 12 dígitos)
            foreach ($candidates as $c) {
                if (strlen($c) >= 4 && strlen($c) <= 12) {
                    return $c;
                }
            }

            return $candidates[0];
        }

        // Fallback: limpa ruídos de barras e mantém apenas dígitos
        $cleaned = preg_replace('/1{4,}/', '', $rawText);

        return preg_replace('/[^0-9]/', '', $cleaned);
    }
}
