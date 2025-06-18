<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Petshop;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $petshop = Petshop::first();

        // Ração
        Product::create([
            'petshop_id' => $petshop->id,
            'name' => 'Ração Premium para Cães Adultos',
            'description' => 'Ração de alta qualidade para cães adultos de todas as raças.',
            'price' => 89.90,
            'stock' => 50,
            'image' => null, // Sem imagem por enquanto
            'is_active' => true,
        ]);

        // Brinquedo
        Product::create([
            'petshop_id' => $petshop->id,
            'name' => 'Bola Interativa para Cães',
            'description' => 'Brinquedo interativo que mantém seu cão entretido e ativo.',
            'price' => 35.50,
            'stock' => 30,
            'image' => null, // Sem imagem por enquanto
            'is_active' => true,
        ]);

        // Acessório
        Product::create([
            'petshop_id' => $petshop->id,
            'name' => 'Coleira Ajustável para Gatos',
            'description' => 'Coleira confortável e ajustável para gatos de todos os tamanhos.',
            'price' => 29.90,
            'stock' => 40,
            'image' => null, // Sem imagem por enquanto
            'is_active' => true,
        ]);
    }
}