<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Petshop;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CouponSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');
        
        // Buscar admin e petshops
        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();
        
        $petshops = Petshop::with('user')->get();
        
        if (!$admin) {
            $this->command->warn('Não há usuário admin. Execute o UserSeeder primeiro.');
            return;
        }

        // Cupons globais (criados pelo admin)
        $globalCoupons = [
            [
                'code' => 'BEMVINDO10',
                'name' => 'Boas-vindas - 10% OFF',
                'description' => 'Desconto especial para novos clientes',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 50,
                'maximum_discount' => 20,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(30),
                'expires_at' => Carbon::now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'FRETE15',
                'name' => 'Frete Grátis + 15% OFF',
                'description' => 'Desconto + frete grátis em compras acima de R$ 100',
                'type' => 'percentage',
                'value' => 15,
                'minimum_amount' => 100,
                'maximum_discount' => 50,
                'usage_limit' => 50,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(15),
                'expires_at' => Carbon::now()->addDays(15),
                'is_active' => true,
            ],
            [
                'code' => 'NATAL2024',
                'name' => 'Natal 2024 - R$ 25 OFF',
                'description' => 'Promoção especial de Natal',
                'type' => 'fixed',
                'value' => 25,
                'minimum_amount' => 80,
                'maximum_discount' => null,
                'usage_limit' => 200,
                'usage_limit_per_user' => 2,
                'starts_at' => Carbon::now()->subDays(45),
                'expires_at' => Carbon::now()->addDays(45),
                'is_active' => true,
            ],
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday - 30% OFF',
                'description' => 'Maior desconto do ano!',
                'type' => 'percentage',
                'value' => 30,
                'minimum_amount' => 150,
                'maximum_discount' => 100,
                'usage_limit' => 300,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(60),
                'expires_at' => Carbon::now()->subDays(30),
                'is_active' => false, // Expirado
            ],
            [
                'code' => 'TESTE10',
                'name' => 'Cupom de Teste - 10% OFF',
                'description' => 'Cupom para testes do sistema',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 30,
                'maximum_discount' => 15,
                'usage_limit' => null, // Ilimitado
                'usage_limit_per_user' => 3,
                'starts_at' => null,
                'expires_at' => null,
                'is_active' => true,
            ]
        ];

        foreach ($globalCoupons as $couponData) {
            $couponData['petshop_id'] = null;
            $couponData['created_by'] = $admin->id;
            $couponData['used_count'] = $faker->numberBetween(0, min($couponData['usage_limit'] ?? 50, 30));
            
            Coupon::create($couponData);
        }

        // Cupons específicos dos pet shops
        foreach ($petshops as $petshop) {
            $petshopCoupons = [
                [
                    'code' => 'PETSHOP' . $petshop->id . '10',
                    'name' => 'Desconto Exclusivo - ' . $petshop->name,
                    'description' => 'Desconto especial da loja ' . $petshop->name,
                    'type' => 'percentage',
                    'value' => 10,
                    'minimum_amount' => 40,
                    'maximum_discount' => 25,
                    'usage_limit' => 50,
                    'usage_limit_per_user' => 2,
                    'starts_at' => Carbon::now()->subDays(20),
                    'expires_at' => Carbon::now()->addDays(40),
                    'is_active' => true,
                ],
                [
                    'code' => 'LOJA' . $petshop->id . '20',
                    'name' => 'Super Desconto - R$ 20 OFF',
                    'description' => 'R$ 20 de desconto em compras na ' . $petshop->name,
                    'type' => 'fixed',
                    'value' => 20,
                    'minimum_amount' => 60,
                    'maximum_discount' => null,
                    'usage_limit' => 30,
                    'usage_limit_per_user' => 1,
                    'starts_at' => Carbon::now()->subDays(10),
                    'expires_at' => Carbon::now()->addDays(20),
                    'is_active' => $faker->boolean(80), // 80% ativo
                ]
            ];

            foreach ($petshopCoupons as $couponData) {
                $couponData['petshop_id'] = $petshop->id;
                $couponData['created_by'] = $petshop->user_id;
                $couponData['used_count'] = $faker->numberBetween(0, min($couponData['usage_limit'], 15));
                
                Coupon::create($couponData);
            }
        }

        // Cupons expirados para mostrar variedade
        $expiredCoupons = [
            [
                'code' => 'VERAO2024',
                'name' => 'Verão 2024 - 20% OFF',
                'description' => 'Promoção de verão (expirada)',
                'type' => 'percentage',
                'value' => 20,
                'minimum_amount' => 70,
                'maximum_discount' => 40,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(120),
                'expires_at' => Carbon::now()->subDays(90),
                'is_active' => false,
                'petshop_id' => null,
                'created_by' => $admin->id,
                'used_count' => 85,
            ],
            [
                'code' => 'PASCOA2024',
                'name' => 'Páscoa 2024 - R$ 15 OFF',
                'description' => 'Promoção de Páscoa (expirada)',
                'type' => 'fixed',
                'value' => 15,
                'minimum_amount' => 50,
                'maximum_discount' => null,
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(200),
                'expires_at' => Carbon::now()->subDays(180),
                'is_active' => false,
                'petshop_id' => null,
                'created_by' => $admin->id,
                'used_count' => 156,
            ]
        ];

        foreach ($expiredCoupons as $couponData) {
            Coupon::create($couponData);
        }

        $totalCoupons = Coupon::count();
        $this->command->info("$totalCoupons cupons criados com sucesso!");
    }
}