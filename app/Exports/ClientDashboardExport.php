<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ClientDashboardExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new ClientStatsSheet($this->data),
            new ClientOrdersSheet($this->data),
            new ClientAppointmentsSheet($this->data),
            new ClientPetsSheet($this->data),
            new ClientSpendingSheet($this->data),
        ];
    }
}

class ClientStatsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([
            [
                'Total de Pedidos',
                $this->data['stats']['total_orders'],
                'Quantidade total de pedidos realizados'
            ],
            [
                'Total Gasto',
                'R$ ' . number_format($this->data['stats']['total_spent'], 2, ',', '.'),
                'Valor total gasto em pedidos'
            ],
            [
                'Total de Agendamentos',
                $this->data['stats']['total_appointments'],
                'Quantidade total de agendamentos'
            ],
            [
                'Total de Pets',
                $this->data['stats']['total_pets'],
                'Quantidade de pets cadastrados'
            ],
            [
                'Valor Médio por Pedido',
                'R$ ' . number_format($this->data['stats']['avg_order_value'] ?? 0, 2, ',', '.'),
                'Valor médio gasto por pedido'
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Métrica',
            'Valor',
            'Descrição'
        ];
    }

    public function title(): string
    {
        return 'Resumo Geral';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
        ];
    }
}

class ClientOrdersSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['recent_orders']->map(function ($order) {
            return [
                'Pedido #' . $order->id,
                $order->created_at->format('d/m/Y H:i'),
                ucfirst($order->status),
                'R$ ' . number_format($order->total_amount, 2, ',', '.'),
                $order->items->count() . ' itens',
                $order->items->first()->product->petshop->name ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Pedido',
            'Data',
            'Status',
            'Valor Total',
            'Itens',
            'Pet Shop'
        ];
    }

    public function title(): string
    {
        return 'Pedidos Recentes';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class ClientAppointmentsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['appointments']->map(function ($appointment) {
            return [
                $appointment->pet->name,
                $appointment->service->name,
                $appointment->service->petshop->name,
                $appointment->appointment_datetime->format('d/m/Y H:i'),
                ucfirst($appointment->status),
                'R$ ' . number_format($appointment->service->price, 2, ',', '.'),
                $appointment->employee->user->name ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Pet',
            'Serviço',
            'Pet Shop',
            'Data/Hora',
            'Status',
            'Valor',
            'Funcionário'
        ];
    }

    public function title(): string
    {
        return 'Agendamentos';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class ClientPetsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['pets']->map(function ($pet) {
            return [
                $pet->name,
                ucfirst($pet->species),
                $pet->breed,
                $pet->birth_date ? $pet->birth_date->format('d/m/Y') : 'N/A',
                ucfirst($pet->gender),
                $pet->appointments_count . ' agendamentos'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Espécie',
            'Raça',
            'Data Nascimento',
            'Gênero',
            'Agendamentos'
        ];
    }

    public function title(): string
    {
        return 'Meus Pets';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class ClientSpendingSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['monthly_spending'])->map(function ($spending) {
            return [
                $spending['month'],
                'R$ ' . number_format($spending['amount'], 2, ',', '.')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Mês',
            'Valor Gasto'
        ];
    }

    public function title(): string
    {
        return 'Gastos Mensais';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}