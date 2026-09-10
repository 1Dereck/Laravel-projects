<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Secretaria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function gerarRelatorioSetor(Local $setor): Response
    {
        $user = auth()->user();
        if ($user?->isUsuario() && $user->setor_id !== $setor->id_local) {
            abort(403, 'Acesso não autorizado ao relatório deste setor.');
        }

        if ($user?->isCoordenador() && ! $user->getSectorLocalIds()->contains($setor->id_local)) {
            abort(403, 'Acesso não autorizado ao relatório deste setor.');
        }

        $setor->load([
            'secretaria',
            'equipamentos.monitores',
            'equipamentos.creator',
            'perifericos.equipamento',
            'perifericos.creator',
        ]);

        $pdf = Pdf::loadView('pdf.relatorio-setor', [
            'local' => $setor,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->stream("relatorio-inventario-setor-{$setor->id_local}.pdf");
    }

    public function gerarRelatorioSecretaria(Secretaria $secretaria): Response
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            abort(403, 'Acesso não autorizado ao relatório de secretaria.');
        }

        if ($user?->isCoordenador() && $user->setor?->secretaria_id !== $secretaria->id_secretarias) {
            abort(403, 'Acesso não autorizado ao relatório desta secretaria.');
        }

        $secretaria->load([
            'locais.equipamentos.monitores',
            'locais.equipamentos.creator',
            'locais.perifericos.equipamento',
            'locais.perifericos.creator',
        ]);

        $pdf = Pdf::loadView('pdf.relatorio-secretaria', [
            'secretaria' => $secretaria,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->stream("relatorio-inventario-secretaria-{$secretaria->id_secretarias}.pdf");
    }

    public function gerarRelatorioQuantidadesSecretaria(int $secretaria = 0): Response
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            abort(403, 'Acesso não autorizado ao relatório de secretaria.');
        }

        $isGeral = ($secretaria === 0);
        $breakdown = [];
        $totalDesktops = 0;
        $totalNotebooks = 0;
        $totalPcs = 0;

        if ($secretaria > 0) {
            $secModel = Secretaria::with(['locais.equipamentos'])->findOrFail($secretaria);

            if ($user?->isCoordenador() && $user->setor?->secretaria_id !== $secModel->id_secretarias) {
                abort(403, 'Acesso não autorizado ao relatório desta secretaria.');
            }

            $titleSecretaria = $secModel->secretaria;
            $nomeExtenso = $secModel->nome_extenso;

            foreach ($secModel->locais as $loc) {
                $desktops = $loc->equipamentos->where('tipo', 'desktop')->count();
                $notebooks = $loc->equipamentos->where('tipo', 'notebook')->count();
                $pcs = $loc->equipamentos->count();

                $totalDesktops += $desktops;
                $totalNotebooks += $notebooks;
                $totalPcs += $pcs;

                $breakdown[] = [
                    'nome' => $loc->local,
                    'subtitulo' => $loc->bairro ? "Bairro: {$loc->bairro}" : null,
                    'desktops' => $desktops,
                    'notebooks' => $notebooks,
                    'total_pcs' => $pcs,
                ];
            }
        } else {
            // Visão Geral Macro (Todas as Secretarias)
            $titleSecretaria = 'Todas as Secretarias (Geral)';
            $nomeExtenso = 'Somatório consolidado de todas as secretarias e setores hospitalares';

            $secQuery = Secretaria::with(['locais.equipamentos']);
            if ($user?->isCoordenador()) {
                $coordSecId = $user->setor?->secretaria_id;
                if ($coordSecId) {
                    $secQuery->where('id_secretarias', $coordSecId);
                }
            }
            $secretarias = $secQuery->orderBy('secretaria')->get();

            foreach ($secretarias as $sec) {
                $secDesktops = 0;
                $secNotebooks = 0;
                $secPcs = 0;
                $locaisCount = $sec->locais->count();

                foreach ($sec->locais as $loc) {
                    $d = $loc->equipamentos->where('tipo', 'desktop')->count();
                    $n = $loc->equipamentos->where('tipo', 'notebook')->count();
                    $p = $loc->equipamentos->count();

                    $secDesktops += $d;
                    $secNotebooks += $n;
                    $secPcs += $p;
                }

                $totalDesktops += $secDesktops;
                $totalNotebooks += $secNotebooks;
                $totalPcs += $secPcs;

                $breakdown[] = [
                    'nome' => $sec->secretaria,
                    'subtitulo' => $sec->nome_extenso ? "{$sec->nome_extenso} — {$locaisCount} local(is)" : "{$locaisCount} local(is)",
                    'desktops' => $secDesktops,
                    'notebooks' => $secNotebooks,
                    'total_pcs' => $secPcs,
                ];
            }

            // Verificar se existem locais sem secretaria que contenham equipamentos
            $locaisSemSec = Local::whereNull('secretaria_id')->with('equipamentos')->get();
            $semSecDesktops = 0;
            $semSecNotebooks = 0;
            $semSecPcs = 0;

            foreach ($locaisSemSec as $locSem) {
                $d = $locSem->equipamentos->where('tipo', 'desktop')->count();
                $n = $locSem->equipamentos->where('tipo', 'notebook')->count();
                $p = $locSem->equipamentos->count();

                $semSecDesktops += $d;
                $semSecNotebooks += $n;
                $semSecPcs += $p;
            }

            if ($semSecPcs > 0) {
                $totalDesktops += $semSecDesktops;
                $totalNotebooks += $semSecNotebooks;
                $totalPcs += $semSecPcs;

                $breakdown[] = [
                    'nome' => 'Outros / Sem Secretaria Definida',
                    'subtitulo' => $locaisSemSec->count().' local(is)',
                    'desktops' => $semSecDesktops,
                    'notebooks' => $semSecNotebooks,
                    'total_pcs' => $semSecPcs,
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.relatorio-quantidades-secretaria', [
            'isGeral' => $isGeral,
            'titleSecretaria' => $titleSecretaria,
            'nomeExtenso' => $nomeExtenso,
            'breakdown' => $breakdown,
            'totalDesktops' => $totalDesktops,
            'totalNotebooks' => $totalNotebooks,
            'totalPcs' => $totalPcs,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->stream('levantamento-quantidades-secretaria-'.($secretaria ?: 'geral').'.pdf');
    }

    public function gerarRelatorioQuantidadesLocal(int $setor = 0): Response
    {
        $user = auth()->user();

        $isGeral = false;
        $locaisBreakdown = [];

        if ($setor > 0) {
            if ($user?->isUsuario() && $user->setor_id !== $setor) {
                abort(403, 'Acesso não autorizado ao relatório deste setor.');
            }

            if ($user?->isCoordenador() && ! $user->getSectorLocalIds()->contains($setor)) {
                abort(403, 'Acesso não autorizado ao relatório deste setor.');
            }

            $locModel = Local::with(['secretaria', 'equipamentos'])->findOrFail($setor);
            $titleLocal = $locModel->local;
            $secretariaNome = $locModel->secretaria?->secretaria;
            $bairro = $locModel->bairro;
            $equipamentos = $locModel->equipamentos;
        } else {
            // Visão Geral Macro (Todos os Locais)
            if ($user?->isUsuario()) {
                $setor = (int) $user->setor_id;
                $locModel = Local::with(['secretaria', 'equipamentos'])->findOrFail($setor);
                $titleLocal = $locModel->local;
                $secretariaNome = $locModel->secretaria?->secretaria;
                $bairro = $locModel->bairro;
                $equipamentos = $locModel->equipamentos;
            } else {
                $isGeral = true;
                $titleLocal = 'Todos os Locais / Setores (Geral)';
                $secretariaNome = null;
                $bairro = null;

                $locsQuery = Local::with(['secretaria', 'equipamentos']);
                if ($user?->isCoordenador()) {
                    $locsQuery->whereIn('id_local', $user->getSectorLocalIds());
                }
                $allLocs = $locsQuery->orderBy('local')->get();

                foreach ($allLocs as $l) {
                    $d = $l->equipamentos->where('tipo', 'desktop')->count();
                    $n = $l->equipamentos->where('tipo', 'notebook')->count();
                    $t = $l->equipamentos->count();

                    $locaisBreakdown[] = [
                        'nome' => $l->local,
                        'secretaria' => $l->secretaria?->secretaria ?? 'N/I',
                        'desktops' => $d,
                        'notebooks' => $n,
                        'total_pcs' => $t,
                    ];
                }

                $equipamentos = $allLocs->flatMap->equipamentos;
            }
        }

        $desktops = $equipamentos->where('tipo', 'desktop')->count();
        $notebooks = $equipamentos->where('tipo', 'notebook')->count();
        $totalPcs = $equipamentos->count();

        $pdf = Pdf::loadView('pdf.relatorio-quantidades-local', [
            'titleLocal' => $titleLocal,
            'secretariaNome' => $secretariaNome,
            'bairro' => $bairro,
            'desktops' => $desktops,
            'notebooks' => $notebooks,
            'totalPcs' => $totalPcs,
            'isGeral' => $isGeral,
            'locaisBreakdown' => $locaisBreakdown,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->stream('levantamento-quantidades-local-'.($setor ?: 'geral').'.pdf');
    }
}
