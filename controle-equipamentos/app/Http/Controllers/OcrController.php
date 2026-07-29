<?php

namespace App\Http\Controllers;

use App\Services\OcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Throwable;

class OcrController extends Controller
{
    public function __construct(
        protected OcrService $ocrService
    ) {}

    /**
     * Processa a imagem capturada ou enviada e lê o número de série via OCR.
     */
    public function readSerial(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp,bmp', 'max:10240'],
        ], [
            'image.required' => 'Nenhuma imagem foi selecionada.',
            'image.file' => 'O arquivo enviado é inválido.',
            'image.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'image.uploaded' => 'Falha ao enviar a foto. A imagem pode exceder o limite do servidor.',
            'image.max' => 'A foto excede o tamanho máximo permitido de 10MB.',
        ]);

        try {
            /** @var UploadedFile $image */
            $image = $request->file('image');
            $text = $this->ocrService->readSerial($image);

            if (empty($text)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nenhum código alfanumérico foi identificado na imagem. Tente aproximação com foco e boa iluminação.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'text' => $text,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar imagem via OCR: '.$e->getMessage(),
            ], 500);
        }
    }
}
