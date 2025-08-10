<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Cliente - {{ $user->name }}</title>
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #007bff;
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
            color: #007bff;
            border-bottom: 1px solid #007bff;
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
        .chart-placeholder {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #007bff;
            font-weight: bold;
            margin: 15px 0;
        }
        .metric-box {
            background-color: #e3f2fd;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 10px 0;
        }
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
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
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🐾 Dashboard Cliente</h1>
        <p><strong>{{ $user->name }}</strong></p>
        <p>{{ $user->email }}</p>
        <p>Relatório gerado em: {{ $generated_at->format('d/m/Y H:i') }}</p>
        <p>Período: {{ $period }}</p>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="section">
        <h2>📊 Resumo Geral</h2>
        
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Total de Pedidos</div>
                <div class="stats-cell stats-header">Total Gasto</div>
                <div class="stats-cell stats-header">Agendamentos</div>
                <div class="stats-cell stats-header">Pets Cadastrados</div>
            </div>
            <div class="stats-row">
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['total_orders'] }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">R$ {{ number_format($stats['total_spent'], 2, ',', '.') }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['total_appointments'] }}</div>
                </div>
                <div class="stats-cell text-center">
                    <div class="metric-value">{{ $stats['total_pets'] }}</div>
                </div>
            </div>
        </div>

        <div class="metric-box">
            <div class="metric-label">Valor Médio por Pedido</div>
            <div class="metric-value">R$ {{ number_format($stats['avg_order_value'] ?? 0, 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Gráfico de Gastos Mensais -->
    <div class="section">
        <h2>💰 Gastos Mensais (Últimos 12 Meses)</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th class="text-right">Valor Gasto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_spending as $spending)
                <tr>
                    <td>{{ $spending['month'] }}</td>
                    <td class="text-right">R$ {{ number_format($spending['amount'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e3f2fd; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-right">R$ {{ number_format(collect($monthly_spending)->sum('amount'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Página 2: Pedidos -->
    <div class="page-break"></div>
    
    <div class="section">
        <h2>🛍️ Pedidos Recentes</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Data</th>
                    <th>Pet Shop</th>
                    <th>Status</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_orders as $order)
                <tr>
                    <td>Pedido #{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->items->first()->product->petshop->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ 
                            $order->status == 'delivered' ? 'success' : 
                            ($order->status == 'shipped' ? 'info' : 
                            ($order->status == 'cancelled' ? 'danger' : 'warning')) 
                        }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="text-right">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Agendamentos -->
    <div class="section">
        <h2>📅 Agendamentos</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Serviço</th>
                    <th>Pet Shop</th>
                    <th>Data/Hora</th>
                    <th>Status</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->pet->name }}</td>
                    <td>{{ $appointment->service->name }}</td>
                    <td>{{ $appointment->service->petshop->name }}</td>
                    <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge badge-{{ 
                            $appointment->status == 'completed' ? 'success' : 
                            ($appointment->status == 'confirmed' ? 'info' : 
                            ($appointment->status == 'cancelled' ? 'danger' : 'warning')) 
                        }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                    <td class="text-right">R$ {{ number_format($appointment->service->price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Página 3: Pets -->
    <div class="page-break"></div>
    
    <div class="section">
        <h2>🐕 Meus Pets</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Data Nascimento</th>
                    <th>Gênero</th>
                    <th class="text-center">Agendamentos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pets as $pet)
                <tr>
                    <td><strong>{{ $pet->name }}</strong></td>
                    <td>{{ ucfirst($pet->species) }}</td>
                    <td>{{ $pet->breed }}</td>
                    <td>{{ $pet->birth_date ? $pet->birth_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ ucfirst($pet->gender) }}</td>
                    <td class="text-center">{{ $pet->appointments_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Categorias -->
    <div class="section">
        <h2>🏷️ Categorias Mais Compradas</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th class="text-center">Itens Comprados</th>
                    <th class="text-right">Total Gasto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_categories as $category)
                <tr>
                    <td>{{ ucfirst($category->category) }}</td>
                    <td class="text-center">{{ $category->total_items }}</td>
                    <td class="text-right">R$ {{ number_format($category->total_spent, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema PetShop Online</p>
        <p>© {{ date('Y') }} - Todos os direitos reservados</p>
        <p>Data e hora de geração: {{ $generated_at->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>