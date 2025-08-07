<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'client');
        })->get();

        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return; // Não pode criar pedidos sem usuários ou produtos
        }

        // Pedido 1 - Status pending
        $order1 = Order::create([
            'user_id' => $users[0]->id,
            'subtotal' => 89.90,
            'total_amount' => 89.90,
            'status' => 'pending',
            'payment_method' => 'credit_card',
            'shipping_address' => $users[0]->address,
            'payment_details' => json_encode(['card_last_digits' => '1234']),
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products[0]->id,
            'quantity' => 1,
            'price' => 89.90,
        ]);

        // Pedido 2 - Status paid
        if ($users->count() > 1) {
            $order2 = Order::create([
                'user_id' => $users[1]->id,
                'subtotal' => 65.40,
                'total_amount' => 65.40,
                'status' => 'paid',
                'payment_method' => 'bank_slip',
                'shipping_address' => $users[1]->address,
                'payment_details' => json_encode(['boleto_code' => 'BOLETO123456']),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $products[1]->id,
                'quantity' => 1,
                'price' => 35.50,
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $products[2]->id,
                'quantity' => 1,
                'price' => 29.90,
            ]);
        }

        // Pedido 3 - Status delivered
        $order3 = Order::create([
            'user_id' => $users[0]->id,
            'subtotal' => 35.50,
            'total_amount' => 35.50,
            'status' => 'delivered',
            'payment_method' => 'pix',
            'shipping_address' => $users[0]->address,
            'payment_details' => json_encode(['pix_key' => 'user@email.com']),
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(7),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $products[1]->id,
            'quantity' => 1,
            'price' => 35.50,
        ]);

        echo "Pedidos criados com sucesso!\n";
    }
}