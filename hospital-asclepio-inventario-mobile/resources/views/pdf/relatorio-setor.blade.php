<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Inventário — {{ $local->local }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #065f46;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 3px 0 0 0;
            font-size: 14px;
            color: #334155;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 9px;
            color: #64748b;
        }
        .meta-info {
            margin-top: 8px;
            font-size: 10px;
            color: #475569;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #047857;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-locado {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-proprio {
            background-color: #f1f5f9;
            color: #475569;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .signature-box {
            margin-top: 40px;
            width: 100%;
        }
        .signature-line {
            border-top: 1px solid #64748b;
            width: 250px;
            margin: 0 auto;
            text-align: center;
            padding-top: 4px;
            font-size: 10px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HOSPITAIS DE CLÍNICAS ASCLÉPIO</h1>
        <h2>Relatório de Parque Tecnológico Hospitalar — {{ $local->local }}</h2>
        <div class="meta-info">
            <span><strong>Ramal / Contato:</strong> {{ $local->telefone ?? 'N/I' }}</span> |
            <span><strong>Localização / Bloco:</strong> {{ $local->rua }} {{ $local->numero ? ', '.$local->numero : '' }} {{ $local->bairro ? '- '.$local->bairro : '' }}</span>
        </div>
        <p>Departamento de Tecnologia da Informação & Comunicação | Gerado em: {{ $dataGeracao }}</p>
    </div>

    <!-- Seção: Computadores e Monitores -->
    <div class="section-title">1. Computadores & Monitores Vinculados</div>
    @if($local->equipamentos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 20%;">Número de Série</th>
                    <th style="width: 25%;">Marca / Modelo</th>
                    <th style="width: 15%;">Kit Teclado/Mouse</th>
                    <th style="width: 30%;">Monitores Vinculados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($local->equipamentos as $eq)
                    <tr>
                        <td style="text-transform: uppercase; font-weight: bold;">{{ $eq->tipo }}</td>
                        <td style="font-family: monospace;">{{ $eq->serial }}</td>
                        <td>{{ $eq->marca_modelo ?? 'N/I' }}</td>
                        <td>
                            @if($eq->kit_teclado_mouse_locado)
                                <span class="badge badge-locado">Locado</span>
                            @else
                                <span class="badge badge-proprio">Próprio</span>
                            @endif
                        </td>
                        <td>
                            @forelse($eq->monitores as $m)
                                <div>#{{ $m->numero }}: @if($m->marca_modelo)<strong>{{ $m->marca_modelo }}</strong> @endif<span style="font-family: monospace;">({{ $m->serial }})</span></div>
                            @empty
                                <span style="color: #94a3b8; italic;">Sem monitores</span>
                            @endforelse
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #64748b; font-style: italic;">Nenhum computador cadastrado para este local.</p>
    @endif

    <!-- Seção: Periféricos Avulsos -->
    <div class="section-title">2. Periféricos & Equipamentos de Apoio</div>
    @if($local->perifericos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Tipo de Periférico</th>
                    <th style="width: 25%;">Patrimônio / Serial</th>
                    <th style="width: 25%;">Computador Vinculado</th>
                    <th style="width: 25%;">Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($local->perifericos as $per)
                    <tr>
                        <td style="font-weight: bold;">{{ $per->tipo }}</td>
                        <td style="font-family: monospace;">{{ $per->serial_patrimonio ?? 'N/I' }}</td>
                        <td>{{ $per->equipamento ? $per->equipamento->serial : 'Avulso no local' }}</td>
                        <td>{{ $per->observacoes ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #64748b; font-style: italic;">Nenhum periférico cadastrado para este local.</p>
    @endif

    <!-- Assinatura de Conferência -->
    <div class="signature-box">
        <div class="signature-line">
            Responsável pelo Levantamento
        </div>
    </div>

    <div class="footer">
        Documento gerado automaticamente pelo Sistema de Inventário — Hospitais Asclépio
    </div>
</body>
</html>
