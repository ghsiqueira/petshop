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

        if (!$petshop) {
            $this->command->warn('Não há pet shops. Execute o PetshopSeeder primeiro.');
            return;
        }

        // Ração
        Product::create([
            'petshop_id' => $petshop->id,
            'name' => 'Ração Premium para Cães Adultos',
            'description' => 'Ração de alta qualidade para cães adultos de todas as raças.',
            'price' => 89.90,
            'category' => 'food',
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
            'category' => 'toys',
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
            'category' => 'accessories',
            'stock' => 40,
            'image' => null, // Sem imagem por enquanto
            'is_active' => true,
        ]);

        // Produto de saúde
        Product::create([
            'petshop_id' => $petshop->id,
            'name' => 'Shampoo Anti-pulgas',
            'description' => 'Shampoo especial para eliminar pulgas e carrapatos.',
            'price' => 24.90,
            'category' => 'health',
            'stock' => 25,
            'image' => null,
            'is_active' => true,
        ]);
    }
}