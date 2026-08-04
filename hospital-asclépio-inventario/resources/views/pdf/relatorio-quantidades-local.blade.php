<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Levantamento de Quantidades — {{ $titleLocal }}</title>
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
        .local-info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
        }
        .local-info-card h3 {
            margin: 0 0 6px 0;
            color: #047857;
            font-size: 13px;
            text-transform: uppercase;
        }
        .local-info-card p {
            margin: 0;
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
        }
        .local-info-card .note {
            margin-top: 10px;
            font-size: 9px;
            color: #64748b;
            font-style: italic;
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
        <h2>Levantamento de Ativos por Unidade / Setor: {{ $titleLocal }}</h2>
        <div class="meta-info">
            @if($secretariaNome)
                <span><strong>Diretoria / Divisão:</strong> {{ $secretariaNome }}</span> |
            @endif
            <span>Gerado em: {{ $dataGeracao }}</span>
            @if($isGeral)
                | <span><strong>Total de Locais:</strong> {{ count($locaisBreakdown) }} local(is)</span>
            @endif
        </div>
        <p>Departamento de Tecnologia da Informação & Comunicação</p>
    </div>

    <!-- Consolidado de Quantidades -->
    <table class="summary-box">
        <tr>
            <td style="background-color: #f8fafc;">
                <div class="summary-title">Desktops</div>
                <div class="summary-value">{{ $desktops }}</div>
            </td>
            <td style="background-color: #f8fafc;">
                <div class="summary-title">Notebooks</div>
                <div class="summary-value">{{ $notebooks }}</div>
            </td>
            <td style="background-color: #ecfdf5;">
                <div class="summary-title" style="color: #047857;">Total de PCs</div>
                <div class="summary-value-total">{{ $totalPcs }}</div>
            </td>
        </tr>
    </table>

    @if($isGeral)
        <div class="section-title">Detalhamento de Quantidades por Local</div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Local / Setor</th>
                    <th style="width: 25%;">Secretaria Vinculada</th>
                    <th style="width: 15%;" class="text-center">Desktops</th>
                    <th style="width: 15%;" class="text-center">Notebooks</th>
                    <th style="width: 15%;" class="text-center">Total de PCs</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locaisBreakdown as $row)
                    <tr>
                        <td><strong>{{ $row['nome'] }}</strong></td>
                        <td>{{ $row['secretaria'] }}</td>
                        <td class="text-center">{{ $row['desktops'] }}</td>
                        <td class="text-center">{{ $row['notebooks'] }}</td>
                        <td class="text-center" style="font-weight: bold; color: #047857;">{{ $row['total_pcs'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #64748b; font-style: italic;">
                            Nenhum local cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($locaisBreakdown) > 0)
                <tfoot>
                    <tr style="background-color: #ecfdf5; font-weight: bold;">
                        <td colspan="2" style="text-transform: uppercase; color: #047857; font-size: 10px;">
                            TOTAL GERAL (SOMATÓRIO DOS LOCAIS)
                        </td>
                        <td class="text-center" style="color: #0f172a; font-size: 11px;">{{ $desktops }}</td>
                        <td class="text-center" style="color: #0f172a; font-size: 11px;">{{ $notebooks }}</td>
                        <td class="text-center" style="color: #047857; font-size: 12px; font-weight: 800;">{{ $totalPcs }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @else
        <div class="local-info-card">
            <h3>Resumo Quantitativo do Local</h3>
            <p>
                O local <strong>{{ $titleLocal }}</strong> possui um total de <strong>{{ $totalPcs }}</strong> equipamento(s) cadastrado(s), sendo <strong>{{ $desktops }}</strong> Desktop(s) e <strong>{{ $notebooks }}</strong> Notebook(s).
            </p>
            <p class="note">
                * Para consultar o relatório detalhado com números de série, marcas, modelos e kits dos computadores, utilize a opção "Busca & Relatório PDF".
            </p>
        </div>
    @endif

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
