<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Administrativo - Relatório Geral</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #6f42c1;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .stats-header {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #6f42c1;
            border-bottom: 1px solid #6f42c1;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .metric-box {
            background-color: #f3e5f5;
            border-left: 4px solid #6f42c1;
            padding: 15px;
            margin: 10px 0;
        }
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #6f42c1;
        }
        .metric-label {
            color: #666;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .growth-positive { color: #28a745; }
        .growth-negative { color: #dc3545; }
        .company-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .highlight-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>⚡ Dashboard Administrativo</h1>
        <h2>Relatório Geral da Plataforma</h2>
        <p>Relatório gerado em: {{ $generated_at->format('d/m/Y H:i') }}</p>
        <p>Período: {{ $period }}</p>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="section">
        <h2>📊 Visão Geral da Plataforma</h2>
        
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Total Usuários</div>
                <div class="stats-cell stats-header">Pet Shops</div>
                <div class="stats-cell stats-header">Produtos</div>
                <div class="stats-cell stats-header">Pedidos</div>
            </div>
            <div class="stats-row">
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ number_format($stats['total_users']) }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ number_format($stats['total_petshops']) }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ number_format($stats['total_products']) }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ number_format($stats['total_orders']) }}</div>
                </div>
            </div>
        </div>

        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; padding-right: 10px;">
                <div class="metric-box">
                    <div class="metric-label">Receita Total da Plataforma</div>
                    <div class="metric-value">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</div>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 10px;">
                <div class="metric-box">
                    <div class="metric-label">Receita do Último Mês</div>
                    <div class="metric-value">R$ {{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Crescimento -->
    <div class="section">
        <h2>📈 Análise de Crescimento</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Métrica</th>
                    <th class="text-center">Valor Atual</th>
                    <th class="text-center">Crescimento</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Novos Usuários</strong></td>
                    <td class="text-center">{{ number_format($growth_stats['current_month_users']) }}</td>
                    <td class="text-center">
                        <span class="{{ $growth_stats['users_growth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                            {{ $growth_stats['users_growth'] >= 0 ? '+' : '' }}{{ number_format($growth_stats['users_growth'], 1) }}%
                        </span>
                    </td>
                    <td>Usuários cadastrados neste mês</td>
                </tr>
                <tr>
                    <td><strong>Receita Mensal</strong></td>
                    <td class="text-center">R$ {{ number_format($growth_stats['current_month_revenue'], 2, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="{{ $growth_stats['revenue_growth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                            {{ $growth_stats['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($growth_stats['revenue_growth'], 1) }}%
                        </span>
                    </td>
                    <td>Receita gerada neste mês</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Usuários por Tipo -->
    <div class="section">
        <h2>👥 Distribuição de Usuários</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Tipo de Usuário</th>
                    <th class="text-center">Quantidade</th>
                    <th class="text-center">Percentual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users_by_role as $user)
                <tr>
                    <td><strong>{{ ucfirst($user->role) }}</strong></td>
                    <td class="text-center">{{ number_format($user->count) }}</td>
                    <td class="text-center">{{ number_format(($user->count / $stats['total_users']) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Página 2: Vendas -->
    <div class="page-break"></div>
    
    <div class="section">
        <h2>💰 Receita Mensal Global (Últimos 12 Meses)</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th class="text-right">Receita</th>
                    <th class="text-center">Variação</th>
                </tr>
            </thead>
            <tbody>
                @php $previousAmount = 0; @endphp
                @foreach($monthly_sales as $index => $sale)
                <tr>
                    <td>{{ $sale['month'] }}</td>
                    <td class="text-right">R$ {{ number_format($sale['amount'], 2, ',', '.') }}</td>
                    <td class="text-center">
                        @if($index > 0 && $previousAmount > 0)
                            @php $growth = (($sale['amount'] - $previousAmount) / $previousAmount) * 100; @endphp
                            <span class="{{ $growth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    @php $previousAmount = $sale['amount']; @endphp
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f3e5f5; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-right">R$ {{ number_format(collect($monthly_sales)->sum('amount'), 2, ',', '.') }}</td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Top Pet Shops -->
    <div class="section">
        <h2>🏆 Top Pet Shops por Receita</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Pet Shop</th>
                    <th class="text-center">Total de Pedidos</th>
                    <th class="text-right">Receita Total</th>
                    <th class="text-center">Participação</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRevenue = collect($top_petshops)->sum('total_revenue'); @endphp
                @foreach($top_petshops as $index => $petshop)
                <tr>
                    <td class="text-center">
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @else {{ $index + 1 }}°
                        @endif
                    </td>
                    <td><strong>{{ $petshop->name }}</strong></td>
                    <td class="text-center">{{ number_format($petshop->total_orders) }}</td>
                    <td class="text-right">R$ {{ number_format($petshop->total_revenue, 2, ',', '.') }}</td>
                    <td class="text-center">
                        {{ $totalRevenue > 0 ? number_format(($petshop->total_revenue / $totalRevenue) * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Insights e Análises -->
    <div class="section">
        <h2>💡 Insights e Análises Estratégicas</h2>
        
        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #6f42c1;">📊 Resumo Executivo</h3>
            
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Pet Shop líder:</strong> 
                    @if($top_petshops->isNotEmpty())
                        {{ $top_petshops->first()->name }} com R$ {{ number_format($top_petshops->first()->total_revenue, 2, ',', '.') }} em receita
                    @else
                        Dados insuficientes
                    @endif
                </li>
                
                <li><strong>Receita média mensal:</strong> 
                    R$ {{ number_format(collect($monthly_sales)->avg('amount'), 2, ',', '.') }}
                </li>
                
                <li><strong>Usuários por pet shop:</strong> 
                    {{ $stats['total_petshops'] > 0 ? number_format($stats['total_users'] / $stats['total_petshops'], 1) : 0 }} usuários/loja
                </li>
                
                <li><strong>Produtos por pet shop:</strong> 
                    {{ $stats['total_petshops'] > 0 ? number_format($stats['total_products'] / $stats['total_petshops'], 1) : 0 }} produtos/loja
                </li>
                
                <li><strong>Ticket médio:</strong> 
                    R$ {{ $stats['total_orders'] > 0 ? number_format($stats['total_revenue'] / $stats['total_orders'], 2, ',', '.') : '0,00' }}
                </li>
            </ul>
        </div>

        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-top: 15px;">
            <h4 style="margin-top: 0; color: #856404;">⚠️ Pontos de Atenção</h4>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @if($growth_stats['users_growth'] < 0)
                    <li>Crescimento de usuários negativo: {{ number_format($growth_stats['users_growth'], 1) }}%</li>
                @endif
                @if($growth_stats['revenue_growth'] < 0)
                    <li>Receita em declínio: {{ number_format($growth_stats['revenue_growth'], 1) }}%</li>
                @endif
                @if($stats['total_petshops'] > 0 && ($stats['total_products'] / $stats['total_petshops']) < 10)
                    <li>Baixa média de produtos por pet shop: {{ number_format($stats['total_products'] / $stats['total_petshops'], 1) }} produtos/loja</li>
                @endif
                <li>Acompanhar regularmente as métricas de crescimento</li>
                <li>Incentivar pet shops com baixo volume de vendas</li>
            </ul>
        </div>

        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-top: 15px;">
            <h4 style="margin-top: 0; color: #155724;">🎯 Oportunidades</h4>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Expandir parcerias com pet shops de alta performance</li>
                <li>Criar programa de incentivos para novos pet shops</li>
                <li>Implementar sistema de recomendações para aumentar ticket médio</li>
                <li>Desenvolver campanhas para retenção de usuários</li>
                <li>Analisar categorias de produtos mais vendidas para insights de mercado</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Relatório Administrativo gerado automaticamente pelo Sistema PetShop Online</p>
        <p>© {{ date('Y') }} - PetShop Online - Todos os direitos reservados</p>
        <p>Data e hora de geração: {{ $generated_at->format('d/m/Y H:i:s') }}</p>
        <p>Este relatório contém informações confidenciais e estratégicas da plataforma</p>
    </div>
</body>
</html>