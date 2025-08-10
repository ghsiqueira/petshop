<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminDashboardExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new AdminStatsSheet($this->data),
            new AdminUsersSheet($this->data),
            new AdminPetshopsSheet($this->data),
            new AdminSalesSheet($this->data),
            new AdminGrowthSheet($this->data),
        ];
    }
}

class AdminStatsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
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
                'Total de Usuários',
                $this->data['stats']['total_users'],
                'Usuários registrados na plataforma'
            ],
            [
                'Total de Pet Shops',
                $this->data['stats']['total_petshops'],
                'Pet Shops cadastrados'
            ],
            [
                'Total de Produtos',
                $this->data['stats']['total_products'],
                'Produtos disponíveis na plataforma'
            ],
            [
                'Total de Pedidos',
                $this->data['stats']['total_orders'],
                'Pedidos realizados'
            ],
            [
                'Receita Total',
                'R$ ' . number_format($this->data['stats']['total_revenue'], 2, ',', '.'),
                'Receita total da plataforma'
            ],
            [
                'Receita Mensal',
                'R$ ' . number_format($this->data['stats']['monthly_revenue'], 2, ',', '.'),
                'Receita do último mês'
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
        return 'Estatísticas Gerais';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class AdminUsersSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['users_by_role'])->map(function ($user) {
            return [
                ucfirst($user->role),
                $user->count,
                round(($user->count / $this->data['stats']['total_users']) * 100, 2) . '%'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tipo de Usuário',
            'Quantidade',
            'Percentual'
        ];
    }

    public function title(): string
    {
        return 'Usuários por Tipo';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class AdminPetshopsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['top_petshops'])->map(function ($petshop) {
            return [
                $petshop->name,
                'R$ ' . number_format($petshop->total_revenue, 2, ',', '.'),
                $petshop->total_orders
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Pet Shop',
            'Receita Total',
            'Total de Pedidos'
        ];
    }

    public function title(): string
    {
        return 'Top Pet Shops';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class AdminSalesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['monthly_sales'])->map(function ($sale) {
            return [
                $sale['month'],
                'R$ ' . number_format($sale['amount'], 2, ',', '.')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Mês',
            'Receita Global'
        ];
    }

    public function title(): string
    {
        return 'Vendas Mensais Globais';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class AdminGrowthSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $growth = $this->data['growth_stats'];
        
        return collect([
            [
                'Crescimento de Usuários',
                number_format($growth['users_growth'], 2) . '%',
                'Variação percentual mês atual vs anterior'
            ],
            [
                'Crescimento de Receita',
                number_format($growth['revenue_growth'], 2) . '%',
                'Variação percentual mês atual vs anterior'
            ],
            [
                'Novos Usuários (Mês Atual)',
                $growth['current_month_users'],
                'Usuários cadastrados neste mês'
            ],
            [
                'Receita (Mês Atual)',
                'R$ ' . number_format($growth['current_month_revenue'], 2, ',', '.'),
                'Receita gerada neste mês'
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
        return 'Crescimento';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}