<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Petshop;
use Faker\Factory as Faker;

class ExtraProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');
        $petshop = Petshop::first();
        
        if (!$petshop) {
            $this->command->warn('Não há pet shops. Execute o PetshopSeeder primeiro.');
            return;
        }

        $products = [
            // Alimentação
            ['name' => 'Ração Golden Mega Cães Adultos 15kg', 'category' => 'food', 'price' => 89.90, 'stock' => 25],
            ['name' => 'Ração Royal Canin Gatos Filhotes 1kg', 'category' => 'food', 'price' => 45.90, 'stock' => 40],
            ['name' => 'Ração Pedigree Cães Pequenos 1kg', 'category' => 'food', 'price' => 12.90, 'stock' => 60],
            ['name' => 'Ração Hill\'s Prescription Diet 2kg', 'category' => 'food', 'price' => 156.90, 'stock' => 15],
            ['name' => 'Sachê Whiskas Gatos Adultos - Salmão', 'category' => 'food', 'price' => 2.50, 'stock' => 100],
            ['name' => 'Biscoito Dog Chow Cães Pequenos', 'category' => 'food', 'price' => 8.90, 'stock' => 45],
            
            // Brinquedos
            ['name' => 'Bola Kong Classic Vermelha P', 'category' => 'toys', 'price' => 35.90, 'stock' => 30],
            ['name' => 'Mordedor Corda Trançada 30cm', 'category' => 'toys', 'price' => 15.90, 'stock' => 50],
            ['name' => 'Ratinho de Pelúcia para Gatos', 'category' => 'toys', 'price' => 9.90, 'stock' => 40],
            ['name' => 'Frisbee para Cães Grande', 'category' => 'toys', 'price' => 25.90, 'stock' => 20],
            ['name' => 'Bolinha com Guizo 6cm', 'category' => 'toys', 'price' => 4.90, 'stock' => 80],
            ['name' => 'Brinquedo Interativo Treat Ball', 'category' => 'toys', 'price' => 42.90, 'stock' => 25],
            
            // Acessórios
            ['name' => 'Coleira Nylon Ajustável M', 'category' => 'accessories', 'price' => 18.90, 'stock' => 45],
            ['name' => 'Guia Retrátil 5m Até 20kg', 'category' => 'accessories', 'price' => 65.90, 'stock' => 20],
            ['name' => 'Bebedouro Fonte Elétrica 2L', 'category' => 'accessories', 'price' => 89.90, 'stock' => 15],
            ['name' => 'Cama Almofada Retangular G', 'category' => 'accessories', 'price' => 79.90, 'stock' => 18],
            ['name' => 'Casinha de Madeira P Cães', 'category' => 'accessories', 'price' => 199.90, 'stock' => 8],
            ['name' => 'Comedouro Duplo Inox Anti-Formiga', 'category' => 'accessories', 'price' => 34.90, 'stock' => 35],
            ['name' => 'Transportadora Plástica N2', 'category' => 'accessories', 'price' => 89.90, 'stock' => 12],
            ['name' => 'Arranhador Torre para Gatos', 'category' => 'accessories', 'price' => 149.90, 'stock' => 10],
            
            // Saúde
            ['name' => 'Shampoo Pelos Claros 500ml', 'category' => 'health', 'price' => 24.90, 'stock' => 30],
            ['name' => 'Antipulgas Advantage Max Cães', 'category' => 'health', 'price' => 67.90, 'stock' => 25],
            ['name' => 'Vermífugo Drontal Plus 4 comprimidos', 'category' => 'health', 'price' => 45.90, 'stock' => 40],
            ['name' => 'Suplemento Vitamínico Pet 60ml', 'category' => 'health', 'price' => 32.90, 'stock' => 35],
            ['name' => 'Escova de Dentes + Pasta Canina', 'category' => 'health', 'price' => 19.90, 'stock' => 50],
            ['name' => 'Protetor Solar Pet Factor 30', 'category' => 'health', 'price' => 28.90, 'stock' => 20],
            ['name' => 'Pomada Cicatrizante Veterinária', 'category' => 'health', 'price' => 15.90, 'stock' => 45],
            
            // Petiscos
            ['name' => 'Petisco Natural Orelha de Porco', 'category' => 'food', 'price' => 12.90, 'stock' => 60],
            ['name' => 'Bifinho de Frango 60g', 'category' => 'food', 'price' => 6.90, 'stock' => 80],
            ['name' => 'Osso Natural Defumado M', 'category' => 'food', 'price' => 8.90, 'stock' => 40],
            ['name' => 'Stick Dental Dog 110g', 'category' => 'food', 'price' => 16.90, 'stock' => 35],
        ];

        foreach ($products as $productData) {
            Product::create([
                'petshop_id' => $petshop->id,
                'name' => $productData['name'],
                'description' => $this->generateDescription($productData['name'], $productData['category']),
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'category' => $productData['category'],
                'image' => null,
                'is_active' => $faker->boolean(90), // 90% dos produtos ativos
            ]);
        }

        $this->command->info(count($products) . ' produtos adicionais criados!');
    }

    private function generateDescription($name, $category)
    {
        $descriptions = [
            'food' => [
                'Alimento completo e balanceado para uma nutrição saudável.',
                'Ingredientes selecionados para o bem-estar do seu pet.',
                'Rico em nutrientes essenciais para uma vida ativa.',
                'Fórmula desenvolvida por veterinários especialistas.',
                'Sabor irresistível que seu pet vai adorar.',
            ],
            'toys' => [
                'Brinquedo resistente e seguro para horas de diversão.',
                'Estimula o desenvolvimento mental e físico.',
                'Material atóxico e durável.',
                'Ideal para exercitar e entreter seu pet.',
                'Ajuda a reduzir o estresse e ansiedade.',
            ],
            'accessories' => [
                'Acessório de alta qualidade para o conforto do seu pet.',
                'Design moderno e funcional.',
                'Material resistente e fácil de limpar.',
                'Proporciona mais comodidade no dia a dia.',
                'Produto pensado para facilitar a rotina com seu pet.',
            ],
            'health' => [
                'Produto para cuidados especiais da saúde pet.',
                'Fórmula desenvolvida por especialistas veterinários.',
                'Contribui para o bem-estar e qualidade de vida.',
                'Ingredientes seguros e eficazes.',
                'Essencial para manter a saúde em dia.',
            ],
        ];

        $categoryDescriptions = $descriptions[$category] ?? $descriptions['accessories'];
        return fake()->randomElement($categoryDescriptions) . ' ' . $name . '.';
    }
}