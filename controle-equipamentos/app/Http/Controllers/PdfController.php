<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function gerarRelatorioSetor(Setor $setor): Response
    {
        $setor->load([
            'equipamentos.monitores',
            'perifericos.equipamento',
        ]);

        $pdf = Pdf::loadView('pdf.relatorio-setor', [
            'setor' => $setor,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->stream("relatorio-inventario-{$setor->id}.pdf");
    }
}
