<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Petshop - {{ $petshop->name }}</title>
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
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #28a745;
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
            color: #28a745;
            border-bottom: 1px solid #28a745;
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
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 10px 0;
        }
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
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
        .company-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .growth-indicator {
            font-size: 10px;
            color: #28a745;
        }
        .growth-negative {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🏪 Relatório do Pet Shop</h1>
        <h2>{{ $petshop->name }}</h2>
        <p>Relatório gerado em: {{ $generated_at->format('d/m/Y H:i') }}</p>
        <p>Período: {{ $period }}</p>
    </div>

    <!-- Informações da Empresa -->
    <div class="company-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 5px;"><strong>Endereço:</strong></td>
                <td style="border: none; padding: 5px;">{{ $petshop->address }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 5px;"><strong>Telefone:</strong></td>
                <td style="border: none; padding: 5px;">{{ $petshop->phone }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 5px;"><strong>Email:</strong></td>
                <td style="border: none; padding: 5px;">{{ $petshop->email }}</td>
            </tr>
            @if($petshop->opening_hours)
            <tr>
                <td style="border: none; padding: 5px;"><strong>Horário:</strong></td>
                <td style="border: none; padding: 5px;">{{ $petshop->opening_hours }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="section">
        <h2>📊 Resumo Geral</h2>
        
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Total Produtos</div>
                <div class="stats-cell stats-header">Produtos Ativos</div>
                <div class="stats-cell stats-header">Total Serviços</div>
                <div class="stats-cell stats-header">Funcionários</div>
            </div>
            <div class="stats-row">
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['total_products'] }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['active_products'] }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['total_services'] }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['total_employees'] }}</div>
                </div>
            </div>
        </div>

        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; padding-right: 10px;">
                <div class="metric-box">
                    <div class="metric-label">Receita Total</div>
                    <div class="metric-value">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</div>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 10px;">
                <div class="metric-box">
                    <div class="metric-label">Receita Mensal</div>
                    <div class="metric-value">R$ {{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendas Mensais -->
    <div class="section">
        <h2>💰 Vendas Mensais (Últimos 12 Meses)</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th class="text-right">Receita</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_sales as $sale)
                <tr>
                    <td>{{ $sale['month'] }}</td>
                    <td class="text-right">R$ {{ number_format($sale['amount'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #d4edda; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-right">R$ {{ number_format(collect($monthly_sales)->sum('amount'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Página 2: Top Produtos -->
    <div class="page-break"></div>
    
    <div class="section">
        <h2>🏆 Top Produtos Mais Vendidos</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Produto</th>
                    <th class="text-center">Quantidade Vendida</th>
                    <th class="text-right">Receita Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_products as $index => $product)
                <tr>
                    <td class="text-center">
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @else {{ $index + 1 }}°
                        @endif
                    </td>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td class="text-center">{{ $product->total_sold }}</td>
                    <td class="text-right">R$ {{ number_format($product->total_revenue, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Serviços -->
    <div class="section">
        <h2>⭐ Top Serviços Mais Agendados</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Serviço</th>
                    <th class="text-center">Total Agendamentos</th>
                    <th class="text-right">Receita Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_services as $index => $service)
                <tr>
                    <td class="text-center">
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @else {{ $index + 1 }}°
                        @endif
                    </td>
                    <td><strong>{{ $service->name }}</strong></td>
                    <td class="text-center">{{ $service->total_appointments }}</td>
                    <td class="text-right">R$ {{ number_format($service->total_revenue, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Página 3: Clientes -->
    <div class="page-break"></div>
    
    <div class="section">
        <h2>👥 Clientes Recentes</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Último Pedido</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_customers as $order)
                <tr>
                    <td><strong>{{ $order->user->name }}</strong></td>
                    <td>{{ $order->user->email }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($order->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Avaliações -->
    @if($reviews->count() > 0)
    <div class="section">
        <h2>⭐ Avaliações Recentes</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Produto/Serviço</th>
                    <th class="text-center">Nota</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews->take(10) as $review)
                <tr>
                    <td>{{ $review->user->name }}</td>
                    <td>{{ $review->reviewable->name }}</td>
                    <td class="text-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating) ⭐ @else ☆ @endif
                        @endfor
                        ({{ $review->rating }})
                    </td>
                    <td>{{ $review->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Insights e Recomendações -->
    <div class="section">
        <h2>💡 Insights e Recomendações</h2>
        
        <div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
            <h3 style="margin-top: 0; color: #1976d2;">📈 Análise de Performance</h3>
            
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Produto mais vendido:</strong> 
                    @if($top_products->isNotEmpty())
                        {{ $top_products->first()->name }} ({{ $top_products->first()->total_sold }} unidades)
                    @else
                        Nenhum produto vendido no período
                    @endif
                </li>
                
                <li><strong>Serviço mais popular:</strong> 
                    @if($top_services->isNotEmpty())
                        {{ $top_services->first()->name }} ({{ $top_services->first()->total_appointments }} agendamentos)
                    @else
                        Nenhum serviço agendado no período
                    @endif
                </li>
                
                <li><strong>Receita média mensal:</strong> 
                    R$ {{ number_format(collect($monthly_sales)->avg('amount'), 2, ',', '.') }}
                </li>
                
                <li><strong>Taxa de produtos ativos:</strong> 
                    {{ $stats['total_products'] > 0 ? round(($stats['active_products'] / $stats['total_products']) * 100, 1) : 0 }}%
                </li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema PetShop Online</p>
        <p>© {{ date('Y') }} - {{ $petshop->name }} - Todos os direitos reservados</p>
        <p>Data e hora de geração: {{ $generated_at->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>