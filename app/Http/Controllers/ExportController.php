<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exportar dashboard do petshop
     */
    public function petshopDashboard($format)
    {
        $user = auth()->user();
        $petshop = $user->petshop;
        
        if (!$petshop) {
            return redirect()->back()->with('error', 'Pet shop não encontrado.');
        }
        
        // Colete todos os dados necessários
        $data = [
            'petshop' => $petshop,
            'totalProducts' => $petshop->products()->count(),
            'activeProducts' => $petshop->products()->where('is_active', true)->count(),
            'totalServices' => $petshop->services()->count(),
            'totalEmployees' => $petshop->employees()->count(),
            'totalRevenue' => $this->calculateTotalRevenue($petshop),
            'monthlyRevenue' => $this->calculateMonthlyRevenue($petshop),
            'topProducts' => $this->getTopProducts($petshop),
            'topServices' => $this->getTopServices($petshop),
            'monthlySales' => $this->getMonthlySales($petshop),
            'recentOrders' => $this->getRecentOrders($petshop)
        ];
        
        if ($format === 'csv') {
            return $this->exportCsv($data);
        }
        
        if ($format === 'xlsx') {
            return $this->exportExcel($data);
        }
        
        if ($format === 'pdf') {
            return $this->exportPdf($data);
        }
        
        return redirect()->back()->with('error', 'Formato não suportado. Use: csv, xlsx ou pdf');
    }

    /**
     * Exportar CSV simples
     */
    private function exportCsv($data)
    {
        $filename = 'petshop_dashboard_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            $this->writeCsvData($file, $data);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar Excel usando PhpSpreadsheet
     */
    private function exportExcel($data)
    {
        try {
            // Verificar se PhpSpreadsheet existe
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                \Log::error('PhpSpreadsheet não encontrado');
                return $this->exportFakeExcel($data); // Fallback para CSV
            }

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Configurar propriedades do documento
            $spreadsheet->getProperties()
                ->setCreator('Petshop System')
                ->setTitle('Dashboard ' . ($data['petshop']->name ?? 'Petshop'))
                ->setSubject('Relatório Dashboard')
                ->setDescription('Relatório completo do dashboard do petshop');
            
            $this->writeExcelData($sheet, $data);
            
            // Criar o writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $filename = 'petshop_dashboard_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // Headers para download
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ];
            
            $callback = function() use ($writer) {
                $writer->save('php://output');
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar Excel: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            // Fallback para CSV com extensão xlsx
            return $this->exportFakeExcel($data);
        }
    }

    /**
     * Exportar PDF usando HTML simples
     */
    private function exportPdf($data)
    {
        try {
            // Gerar HTML para PDF
            $html = $this->generateHtmlReport($data);
            
            $filename = 'petshop_dashboard_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Se tiver DOMPDF instalado, usar ele
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                return $pdf->download($filename);
            }
            
            // Fallback: retornar HTML que o navegador pode imprimir como PDF
            return $this->exportHtmlToPrint($data);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao gerar PDF. Tente CSV ou Excel.');
        }
    }

    /**
     * Fallback: CSV com headers de Excel
     */
    private function exportFakeExcel($data)
    {
        $filename = 'petshop_dashboard_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            $this->writeCsvData($file, $data);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Fallback: HTML para impressão como PDF
     */
    private function exportHtmlToPrint($data)
    {
        $html = $this->generateHtmlReport($data);
        
        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8',
        ];
        
        // Adicionar CSS para impressão e botão de print
        $printHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Dashboard - ' . ($data['petshop']->name ?? 'Petshop') . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .print-button { margin-bottom: 20px; }
                @media print {
                    .print-button { display: none; }
                }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                h1, h2 { color: #333; }
                .metric-value { font-weight: bold; color: #007bff; }
                .section { margin-bottom: 30px; }
            </style>
        </head>
        <body>
            <div class="print-button">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">
                    📄 Imprimir / Salvar como PDF
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; margin-left: 10px; border-radius: 4px;">
                    ✖ Fechar
                </button>
            </div>
            ' . $html . '
        </body>
        </html>';
        
        return response($printHtml, 200, $headers);
    }

    private function generateHtmlReport($data)
    {
        $html = '<div class="section">';
        $html .= '<h1>📊 Dashboard - Visão Geral do Petshop</h1>';
        $html .= '<p><strong>🏪 Petshop:</strong> ' . ($data['petshop']->name ?? '') . '</p>';
        $html .= '<p><strong>📅 Data de Geração:</strong> ' . date('d/m/Y H:i') . '</p>';
        $html .= '</div>';
        
        // Métricas principais
        $html .= '<div class="section">';
        $html .= '<h2>📈 Métricas Principais</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Métrica</th><th>Valor</th></tr>';
        $html .= '<tr><td>📦 Total de Produtos</td><td class="metric-value">' . ($data['totalProducts'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>✅ Produtos Ativos</td><td class="metric-value">' . ($data['activeProducts'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>🛠 Total de Serviços</td><td class="metric-value">' . ($data['totalServices'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>👥 Total de Funcionários</td><td class="metric-value">' . ($data['totalEmployees'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>💰 Receita Total</td><td class="metric-value">R$ ' . number_format($data['totalRevenue'] ?? 0, 2, ',', '.') . '</td></tr>';
        $html .= '<tr><td>📊 Receita Mensal</td><td class="metric-value">R$ ' . number_format($data['monthlyRevenue'] ?? 0, 2, ',', '.') . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Produtos mais vendidos
        $html .= '<div class="section">';
        $html .= '<h2>🏆 Produtos Mais Vendidos</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Produto</th><th>Quantidade Vendida</th><th>Receita Total</th></tr>';
        
        if (isset($data['topProducts']) && count($data['topProducts']) > 0) {
            foreach ($data['topProducts'] as $product) {
                $html .= '<tr>';
                $html .= '<td>' . ($product->name ?? 'N/A') . '</td>';
                $html .= '<td>' . ($product->total_sold ?? 0) . '</td>';
                $html .= '<td>R$ ' . number_format($product->total_revenue ?? 0, 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="3">Nenhum produto vendido ainda</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        
        // Serviços mais agendados
        $html .= '<div class="section">';
        $html .= '<h2>🛠 Serviços Mais Agendados</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Serviço</th><th>Total de Agendamentos</th><th>Receita Total</th></tr>';
        
        if (isset($data['topServices']) && count($data['topServices']) > 0) {
            foreach ($data['topServices'] as $service) {
                $html .= '<tr>';
                $html .= '<td>' . ($service->name ?? 'N/A') . '</td>';
                $html .= '<td>' . ($service->total_appointments ?? 0) . '</td>';
                $html .= '<td>R$ ' . number_format($service->total_revenue ?? 0, 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="3">Nenhum serviço agendado ainda</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        
        // Vendas mensais
        $html .= '<div class="section">';
        $html .= '<h2>📅 Vendas Mensais - Últimos 12 Meses</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Mês</th><th>Receita (R$)</th></tr>';
        
        if (isset($data['monthlySales']) && count($data['monthlySales']) > 0) {
            foreach ($data['monthlySales'] as $sale) {
                $html .= '<tr>';
                $html .= '<td>' . ($sale['month'] ?? 'N/A') . '</td>';
                $html .= '<td>R$ ' . number_format($sale['revenue'] ?? 0, 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="2">Nenhuma venda registrada</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        
        // Pedidos recentes
        $html .= '<div class="section">';
        $html .= '<h2>📋 Pedidos Recentes</h2>';
        $html .= '<table>';
        $html .= '<tr><th>ID</th><th>Cliente</th><th>Data</th><th>Status</th><th>Total</th></tr>';
        
        if (isset($data['recentOrders']) && count($data['recentOrders']) > 0) {
            foreach ($data['recentOrders'] as $order) {
                $html .= '<tr>';
                $html .= '<td>' . ($order->id ?? 'N/A') . '</td>';
                $html .= '<td>' . ($order->user->name ?? 'N/A') . '</td>';
                $html .= '<td>' . ($order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A') . '</td>';
                $html .= '<td>' . $this->getStatusInPortuguese($order->status ?? 'N/A') . '</td>';
                $html .= '<td>R$ ' . number_format($order->total_amount ?? 0, 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="5">Nenhum pedido registrado</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }

    private function writeCsvData($file, $data)
    {
        // Seção 1: Informações Gerais
        fputcsv($file, ['DASHBOARD - VISÃO GERAL DO PETSHOP'], ';');
        fputcsv($file, ['Petshop:', $data['petshop']->name ?? ''], ';');
        fputcsv($file, ['Data de Geração:', date('d/m/Y H:i')], ';');
        fputcsv($file, [''], ';');
        
        // Seção 2: Métricas Principais
        fputcsv($file, ['MÉTRICAS PRINCIPAIS'], ';');
        fputcsv($file, ['Métrica', 'Valor'], ';');
        fputcsv($file, ['Total de Produtos', $data['totalProducts'] ?? 0], ';');
        fputcsv($file, ['Produtos Ativos', $data['activeProducts'] ?? 0], ';');
        fputcsv($file, ['Total de Serviços', $data['totalServices'] ?? 0], ';');
        fputcsv($file, ['Total de Funcionários', $data['totalEmployees'] ?? 0], ';');
        fputcsv($file, ['Receita Total', 'R$ ' . number_format($data['totalRevenue'] ?? 0, 2, ',', '.')], ';');
        fputcsv($file, ['Receita Mensal', 'R$ ' . number_format($data['monthlyRevenue'] ?? 0, 2, ',', '.')], ';');
        fputcsv($file, [''], ';');
        
        // Seção 3: Produtos Mais Vendidos
        fputcsv($file, ['PRODUTOS MAIS VENDIDOS'], ';');
        fputcsv($file, ['Produto', 'Quantidade Vendida', 'Receita Total'], ';');
        
        if (isset($data['topProducts']) && count($data['topProducts']) > 0) {
            foreach ($data['topProducts'] as $product) {
                fputcsv($file, [
                    $product->name ?? 'N/A',
                    $product->total_sold ?? 0,
                    'R$ ' . number_format($product->total_revenue ?? 0, 2, ',', '.')
                ], ';');
            }
        } else {
            fputcsv($file, ['Nenhum produto vendido ainda', '', ''], ';');
        }
        
        fputcsv($file, [''], ';');
        
        // Seção 4: Serviços Mais Agendados
        fputcsv($file, ['SERVIÇOS MAIS AGENDADOS'], ';');
        fputcsv($file, ['Serviço', 'Total de Agendamentos', 'Receita Total'], ';');
        
        if (isset($data['topServices']) && count($data['topServices']) > 0) {
            foreach ($data['topServices'] as $service) {
                fputcsv($file, [
                    $service->name ?? 'N/A',
                    $service->total_appointments ?? 0,
                    'R$ ' . number_format($service->total_revenue ?? 0, 2, ',', '.')
                ], ';');
            }
        } else {
            fputcsv($file, ['Nenhum serviço agendado ainda', '', ''], ';');
        }
        
        fputcsv($file, [''], ';');
        
        // Seção 5: Vendas Mensais
        fputcsv($file, ['VENDAS MENSAIS - ÚLTIMOS 12 MESES'], ';');
        fputcsv($file, ['Mês', 'Receita (R$)'], ';');
        
        if (isset($data['monthlySales']) && count($data['monthlySales']) > 0) {
            foreach ($data['monthlySales'] as $sale) {
                fputcsv($file, [
                    $sale['month'] ?? 'N/A',
                    'R$ ' . number_format($sale['revenue'] ?? 0, 2, ',', '.')
                ], ';');
            }
        } else {
            fputcsv($file, ['Nenhuma venda registrada', ''], ';');
        }
        
        fputcsv($file, [''], ';');
        
        // Seção 6: Pedidos Recentes
        fputcsv($file, ['PEDIDOS RECENTES'], ';');
        fputcsv($file, ['ID', 'Cliente', 'Data', 'Status', 'Total'], ';');
        
        if (isset($data['recentOrders']) && count($data['recentOrders']) > 0) {
            foreach ($data['recentOrders'] as $order) {
                fputcsv($file, [
                    $order->id ?? 'N/A',
                    $order->user->name ?? 'N/A',
                    $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A',
                    $this->getStatusInPortuguese($order->status ?? 'N/A'),
                    'R$ ' . number_format($order->total_amount ?? 0, 2, ',', '.')
                ], ';');
            }
        } else {
            fputcsv($file, ['Nenhum pedido registrado', '', '', '', ''], ';');
        }
    }

    private function writeExcelData($sheet, $data)
    {
        $row = 1;
        
        // Título principal
        $sheet->setCellValue('A' . $row, 'DASHBOARD - VISÃO GERAL DO PETSHOP');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Petshop: ' . ($data['petshop']->name ?? ''));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Data de Geração: ' . date('d/m/Y H:i'));
        $row += 2;
        
        // Métricas Principais
        $sheet->setCellValue('A' . $row, 'MÉTRICAS PRINCIPAIS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('FFE6E6FA');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $row++;
        
        $metrics = [
            ['Total de Produtos', $data['totalProducts'] ?? 0],
            ['Produtos Ativos', $data['activeProducts'] ?? 0],
            ['Total de Serviços', $data['totalServices'] ?? 0],
            ['Total de Funcionários', $data['totalEmployees'] ?? 0],
            ['Receita Total', 'R$ ' . number_format($data['totalRevenue'] ?? 0, 2, ',', '.')],
            ['Receita Mensal', 'R$ ' . number_format($data['monthlyRevenue'] ?? 0, 2, ',', '.')]
        ];
        
        foreach ($metrics as $metric) {
            $sheet->setCellValue('A' . $row, $metric[0]);
            $sheet->setCellValue('B' . $row, $metric[1]);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }
        
        $row++;
        
        // Produtos Mais Vendidos
        $sheet->setCellValue('A' . $row, 'PRODUTOS MAIS VENDIDOS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('FFE6FFE6');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Produto');
        $sheet->setCellValue('B' . $row, 'Quantidade Vendida');
        $sheet->setCellValue('C' . $row, 'Receita Total');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->getStartColor()->setARGB('FFF0F0F0');
        $row++;
        
        if (isset($data['topProducts']) && count($data['topProducts']) > 0) {
            foreach ($data['topProducts'] as $product) {
                $sheet->setCellValue('A' . $row, $product->name ?? 'N/A');
                $sheet->setCellValue('B' . $row, $product->total_sold ?? 0);
                $sheet->setCellValue('C' . $row, 'R$ ' . number_format($product->total_revenue ?? 0, 2, ',', '.'));
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Nenhum produto vendido ainda');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $row++;
        }
        
        $row++;
        
        // Serviços Mais Agendados
        $sheet->setCellValue('A' . $row, 'SERVIÇOS MAIS AGENDADOS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('FFFFE6E6');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Serviço');
        $sheet->setCellValue('B' . $row, 'Total de Agendamentos');
        $sheet->setCellValue('C' . $row, 'Receita Total');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->getStartColor()->setARGB('FFF0F0F0');
        $row++;
        
        if (isset($data['topServices']) && count($data['topServices']) > 0) {
            foreach ($data['topServices'] as $service) {
                $sheet->setCellValue('A' . $row, $service->name ?? 'N/A');
                $sheet->setCellValue('B' . $row, $service->total_appointments ?? 0);
                $sheet->setCellValue('C' . $row, 'R$ ' . number_format($service->total_revenue ?? 0, 2, ',', '.'));
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Nenhum serviço agendado ainda');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $row++;
        }
        
        $row++;
        
        // Vendas Mensais
        $sheet->setCellValue('A' . $row, 'VENDAS MENSAIS - ÚLTIMOS 12 MESES');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('FFFFEAA7');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Mês');
        $sheet->setCellValue('B' . $row, 'Receita (R$)');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->getStartColor()->setARGB('FFF0F0F0');
        $row++;
        
        if (isset($data['monthlySales']) && count($data['monthlySales']) > 0) {
            foreach ($data['monthlySales'] as $sale) {
                $sheet->setCellValue('A' . $row, $sale['month'] ?? 'N/A');
                $sheet->setCellValue('B' . $row, 'R$ ' . number_format($sale['revenue'] ?? 0, 2, ',', '.'));
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Nenhuma venda registrada');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $row++;
        }
        
        $row++;
        
        // Pedidos Recentes
        $sheet->setCellValue('A' . $row, 'PEDIDOS RECENTES');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('FFE6F3FF');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'ID');
        $sheet->setCellValue('B' . $row, 'Cliente');
        $sheet->setCellValue('C' . $row, 'Data');
        $sheet->setCellValue('D' . $row, 'Status');
        $sheet->setCellValue('E' . $row, 'Total');
        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->getStartColor()->setARGB('FFF0F0F0');
        $row++;
        
        if (isset($data['recentOrders']) && count($data['recentOrders']) > 0) {
            foreach ($data['recentOrders'] as $order) {
                $sheet->setCellValue('A' . $row, $order->id ?? 'N/A');
                $sheet->setCellValue('B' . $row, $order->user->name ?? 'N/A');
                $sheet->setCellValue('C' . $row, $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A');
                $sheet->setCellValue('D' . $row, $this->getStatusInPortuguese($order->status ?? 'N/A'));
                $sheet->setCellValue('E' . $row, 'R$ ' . number_format($order->total_amount ?? 0, 2, ',', '.'));
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Nenhum pedido registrado');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $row++;
        }
        
        // Auto-ajustar largura das colunas
        foreach(range('A','E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Aplicar bordas em toda a planilha
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        
        $sheet->getStyle('A1:E' . ($row - 1))->applyFromArray($styleArray);
    }

    private function getStatusInPortuguese($status)
    {
        $statusMap = [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];
        
        return $statusMap[$status] ?? ucfirst($status);
    }

    private function calculateTotalRevenue($petshop)
    {
        return Order::whereHas('items.product', function ($query) use ($petshop) {
            $query->where('petshop_id', $petshop->id);
        })->whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_amount') ?? 0;
    }

    private function calculateMonthlyRevenue($petshop)
    {
        $lastMonth = Carbon::now()->subMonth();
        return Order::whereHas('items.product', function ($query) use ($petshop) {
            $query->where('petshop_id', $petshop->id);
        })
        ->where('created_at', '>=', $lastMonth)
        ->whereIn('status', ['paid', 'shipped', 'delivered'])
        ->sum('total_amount') ?? 0;
    }

    private function getTopProducts($petshop)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.petshop_id', $petshop->id)
            ->whereIn('orders.status', ['paid', 'shipped', 'delivered'])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTopServices($petshop)
    {
        return DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('services.petshop_id', $petshop->id)
            ->whereIn('appointments.status', ['confirmed', 'completed'])
            ->select('services.name', DB::raw('COUNT(*) as total_appointments'), DB::raw('SUM(services.price) as total_revenue'))
            ->groupBy('services.id', 'services.name', 'services.price')
            ->orderBy('total_appointments', 'desc')
            ->limit(10)
            ->get();
    }

    private function getMonthlySales($petshop)
    {
        $sales = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthSales = Order::whereHas('items.product', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereIn('status', ['paid', 'shipped', 'delivered'])
            ->sum('total_amount') ?? 0;
            
            $sales[] = [
                'month' => $month->format('M/Y'),
                'revenue' => $monthSales
            ];
        }
        
        return $sales;
    }

    private function getRecentOrders($petshop)
    {
        return Order::whereHas('items.product', function ($query) use ($petshop) {
            $query->where('petshop_id', $petshop->id);
        })
        ->with(['user', 'items.product'])
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get();
    }

    /**
     * Exportar dashboard do cliente (se necessário)
     */
    public function clientDashboard($format)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('client')) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }
        
        // Dados do cliente
        $data = [
            'user' => $user,
            'totalOrders' => $user->orders()->count(),
            'totalSpent' => $user->orders()->whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_amount'),
            'recentOrders' => $user->orders()->orderBy('created_at', 'desc')->limit(10)->get(),
            'totalAppointments' => $user->appointments()->count(),
            'recentAppointments' => $user->appointments()->orderBy('appointment_datetime', 'desc')->limit(10)->get(),
        ];
        
        if ($format === 'csv') {
            return $this->exportClientCsv($data);
        }

        if ($format === 'xlsx') {
            return $this->exportExcel($data);
        }
        
        if ($format === 'pdf') {
            return $this->exportClientPdf($data);
        }
        
        return redirect()->back()->with('error', 'Formato não suportado. Use: csv ou pdf');
    }

    private function exportClientCsv($data)
    {
        $filename = 'cliente_dashboard_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Informações do cliente
            fputcsv($file, ['DASHBOARD DO CLIENTE'], ';');
            fputcsv($file, ['Cliente:', $data['user']->name ?? ''], ';');
            fputcsv($file, ['Email:', $data['user']->email ?? ''], ';');
            fputcsv($file, ['Data de Geração:', date('d/m/Y H:i')], ';');
            fputcsv($file, [''], ';');
            
            // Estatísticas
            fputcsv($file, ['ESTATÍSTICAS'], ';');
            fputcsv($file, ['Total de Pedidos', $data['totalOrders'] ?? 0], ';');
            fputcsv($file, ['Total Gasto', 'R$ ' . number_format($data['totalSpent'] ?? 0, 2, ',', '.')], ';');
            fputcsv($file, ['Total de Agendamentos', $data['totalAppointments'] ?? 0], ';');
            fputcsv($file, [''], ';');
            
            // Pedidos recentes
            fputcsv($file, ['PEDIDOS RECENTES'], ';');
            fputcsv($file, ['ID', 'Data', 'Status', 'Total'], ';');
            
            if (isset($data['recentOrders']) && count($data['recentOrders']) > 0) {
                foreach ($data['recentOrders'] as $order) {
                    fputcsv($file, [
                        $order->id ?? 'N/A',
                        $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A',
                        $this->getStatusInPortuguese($order->status ?? 'N/A'),
                        'R$ ' . number_format($order->total_amount ?? 0, 2, ',', '.')
                    ], ';');
                }
            } else {
                fputcsv($file, ['Nenhum pedido encontrado', '', '', ''], ';');
            }
            
            fputcsv($file, [''], ';');
            
            // Agendamentos recentes
            fputcsv($file, ['AGENDAMENTOS RECENTES'], ';');
            fputcsv($file, ['ID', 'Serviço', 'Data', 'Status'], ';');
            
            if (isset($data['recentAppointments']) && count($data['recentAppointments']) > 0) {
                foreach ($data['recentAppointments'] as $appointment) {
                    fputcsv($file, [
                        $appointment->id ?? 'N/A',
                        $appointment->service->name ?? 'N/A',
                        $appointment->appointment_datetime ? Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') : 'N/A',
                        $this->getStatusInPortuguese($appointment->status ?? 'N/A')
                    ], ';');
                }
            } else {
                fputcsv($file, ['Nenhum agendamento encontrado', '', '', ''], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportClientPdf($data)
    {
        $html = '<h1>📱 Dashboard do Cliente</h1>';
        $html .= '<p><strong>👤 Cliente:</strong> ' . ($data['user']->name ?? '') . '</p>';
        $html .= '<p><strong>📧 Email:</strong> ' . ($data['user']->email ?? '') . '</p>';
        $html .= '<p><strong>📅 Data de Geração:</strong> ' . date('d/m/Y H:i') . '</p>';
        
        $html .= '<h2>📊 Estatísticas</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Métrica</th><th>Valor</th></tr>';
        $html .= '<tr><td>📦 Total de Pedidos</td><td>' . ($data['totalOrders'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>💰 Total Gasto</td><td>R$ ' . number_format($data['totalSpent'] ?? 0, 2, ',', '.') . '</td></tr>';
        $html .= '<tr><td>📅 Total de Agendamentos</td><td>' . ($data['totalAppointments'] ?? 0) . '</td></tr>';
        $html .= '</table>';
        
        return $this->exportHtmlToPrint(['client_html' => $html]);
    }

    /**
     * Exportar dashboard do admin (se necessário)
     */
    public function adminDashboard($format)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }
        
        // Dados do admin - estatísticas gerais do sistema
        $data = [
            'totalUsers' => \App\Models\User::count(),
            'totalPetshops' => \App\Models\Petshop::count(),
            'totalProducts' => \App\Models\Product::count(),
            'totalOrders' => \App\Models\Order::count(),
            'totalRevenue' => \App\Models\Order::whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_amount'),
            'recentUsers' => \App\Models\User::orderBy('created_at', 'desc')->limit(10)->get(),
            'recentOrders' => \App\Models\Order::orderBy('created_at', 'desc')->limit(10)->get(),
        ];
        
        if ($format === 'csv') {
            return $this->exportAdminCsv($data);
        }

        if ($format === 'xlsx') {
            return $this->exportExcel($data);
        }
        
        if ($format === 'pdf') {
            return $this->exportAdminPdf($data);
        }
        
        return redirect()->back()->with('error', 'Formato não suportado. Use: csv ou pdf');
    }

    private function exportAdminCsv($data)
    {
        $filename = 'admin_dashboard_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Informações do sistema
            fputcsv($file, ['DASHBOARD ADMINISTRATIVO'], ';');
            fputcsv($file, ['Data de Geração:', date('d/m/Y H:i')], ';');
            fputcsv($file, [''], ';');
            
            // Estatísticas gerais
            fputcsv($file, ['ESTATÍSTICAS GERAIS DO SISTEMA'], ';');
            fputcsv($file, ['Total de Usuários', $data['totalUsers'] ?? 0], ';');
            fputcsv($file, ['Total de Petshops', $data['totalPetshops'] ?? 0], ';');
            fputcsv($file, ['Total de Produtos', $data['totalProducts'] ?? 0], ';');
            fputcsv($file, ['Total de Pedidos', $data['totalOrders'] ?? 0], ';');
            fputcsv($file, ['Receita Total do Sistema', 'R$ ' . number_format($data['totalRevenue'] ?? 0, 2, ',', '.')], ';');
            fputcsv($file, [''], ';');
            
            // Usuários recentes
            fputcsv($file, ['USUÁRIOS RECENTES'], ';');
            fputcsv($file, ['ID', 'Nome', 'Email', 'Data de Cadastro'], ';');
            
            if (isset($data['recentUsers']) && count($data['recentUsers']) > 0) {
                foreach ($data['recentUsers'] as $user) {
                    fputcsv($file, [
                        $user->id ?? 'N/A',
                        $user->name ?? 'N/A',
                        $user->email ?? 'N/A',
                        $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A'
                    ], ';');
                }
            } else {
                fputcsv($file, ['Nenhum usuário encontrado', '', '', ''], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportAdminPdf($data)
    {
        $html = '<h1>⚙️ Dashboard Administrativo</h1>';
        $html .= '<p><strong>📅 Data de Geração:</strong> ' . date('d/m/Y H:i') . '</p>';
        
        $html .= '<h2>📊 Estatísticas Gerais do Sistema</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Métrica</th><th>Valor</th></tr>';
        $html .= '<tr><td>👥 Total de Usuários</td><td>' . ($data['totalUsers'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>🏪 Total de Petshops</td><td>' . ($data['totalPetshops'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>📦 Total de Produtos</td><td>' . ($data['totalProducts'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>🛒 Total de Pedidos</td><td>' . ($data['totalOrders'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>💰 Receita Total</td><td>R$ ' . number_format($data['totalRevenue'] ?? 0, 2, ',', '.') . '</td></tr>';
        $html .= '</table>';
        
        return $this->exportHtmlToPrint(['admin_html' => $html]);
    }
}