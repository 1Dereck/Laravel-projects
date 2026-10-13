<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Levantamento de Quantidades — {{ $titleSecretaria }}</title>
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
            color: #047857;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 3px 0 0 0;
            font-size: 14px;
            color: #1e293b;
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
        .summary-box {
            margin-bottom: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            width: 33.33%;
        }
        .summary-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }
        .summary-value-total {
            font-size: 20px;
            font-weight: bold;
            color: #047857;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #047857;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center !important;
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
        <h2>Levantamento Geral de Ativos de TI: {{ $titleSecretaria }}</h2>
        <div class="meta-info">
            @if($nomeExtenso)
                <span><strong>Denominação:</strong> {{ $nomeExtenso }}</span> |
            @endif
            <span><strong>Total de {{ $isGeral ? 'Diretorias/Divisões' : 'Unidades/Setores' }}:</strong> {{ count($breakdown) }} {{ $isGeral ? 'divisão(ões)' : 'unidade(s)' }}</span>
        </div>
        <p>Departamento de Tecnologia da Informação & Comunicação | Gerado em: {{ $dataGeracao }}</p>
    </div>

    <!-- Consolidado Geral -->
    <table class="summary-box">
        <tr>
            <td style="background-color: #f8fafc;">
                <div class="summary-title">Desktops</div>
                <div class="summary-value">{{ $totalDesktops }}</div>
            </td>
            <td style="background-color: #f8fafc;">
                <div class="summary-title">Notebooks</div>
                <div class="summary-value">{{ $totalNotebooks }}</div>
            </td>
            <td style="background-color: #ecfdf5;">
                <div class="summary-title" style="color: #047857;">Total de PCs</div>
                <div class="summary-value-total">{{ $totalPcs }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">{{ $isGeral ? 'Detalhamento por Secretaria' : 'Detalhamento por Setor / Local' }}</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">{{ $isGeral ? 'Secretaria' : 'Setor / Local' }}</th>
                <th style="width: 20%;" class="text-center">Desktops</th>
                <th style="width: 20%;" class="text-center">Notebooks</th>
                <th style="width: 20%;" class="text-center">Total de PCs</th>
            </tr>
        </thead>
        <tbody>
            @forelse($breakdown as $row)
                <tr>
                    <td>
                        <strong>{{ $row['nome'] }}</strong>
                        @if(!empty($row['subtitulo']))
                            <br><span style="font-size: 9px; color: #64748b;">{{ $row['subtitulo'] }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $row['desktops'] }}</td>
                    <td class="text-center">{{ $row['notebooks'] }}</td>
                    <td class="text-center" style="font-weight: bold; color: #047857;">{{ $row['total_pcs'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #64748b; font-style: italic;">
                        Nenhum registro cadastrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($breakdown) > 0)
            <tfoot>
                <tr style="background-color: #ecfdf5; font-weight: bold;">
                    <td style="text-transform: uppercase; color: #047857; font-size: 10px;">
                        TOTAL GERAL ({{ $isGeral ? 'SOMATÓRIO DE TODAS AS SECRETARIAS' : 'SOMATÓRIO DOS SETORES' }})
                    </td>
                    <td class="text-center" style="color: #0f172a; font-size: 11px;">{{ $totalDesktops }}</td>
                    <td class="text-center" style="color: #0f172a; font-size: 11px;">{{ $totalNotebooks }}</td>
                    <td class="text-center" style="color: #047857; font-size: 12px; font-weight: 800;">{{ $totalPcs }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

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
