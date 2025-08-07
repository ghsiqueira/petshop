<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Product;
use App\Models\Service;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Encontrar clientes com pedidos entregues (delivered, não completed)
        $deliveredOrders = Order::where('status', 'delivered')
                               ->with(['user', 'items.product'])
                               ->get();

        if ($deliveredOrders->isEmpty()) {
            echo "Não há clientes com pedidos entregues. Execute o OrderSeeder primeiro.\n";
            return;
        }

        // Criar avaliações para produtos de pedidos entregues
        foreach ($deliveredOrders as $order) {
            foreach ($order->items as $item) {
                // Verificar se já existe avaliação deste usuário para este produto
                $existingReview = Review::where('user_id', $order->user_id)
                                      ->where('reviewable_type', Product::class)
                                      ->where('reviewable_id', $item->product_id)
                                      ->first();

                if (!$existingReview) {
                    Review::create([
                        'user_id' => $order->user_id,
                        'reviewable_type' => Product::class,
                        'reviewable_id' => $item->product_id,
                        'rating' => rand(4, 5), // Avaliações positivas (4-5 estrelas)
                        'comment' => $this->getRandomProductComment(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Encontrar agendamentos completados para criar avaliações de serviços
        $completedAppointments = Appointment::where('status', 'completed')
                                           ->with(['user', 'service'])
                                           ->get();

        foreach ($completedAppointments as $appointment) {
            // Verificar se já existe avaliação deste usuário para este serviço
            $existingServiceReview = Review::where('user_id', $appointment->user_id)
                                          ->where('reviewable_type', Service::class)
                                          ->where('reviewable_id', $appointment->service_id)
                                          ->first();

            if (!$existingServiceReview) {
                Review::create([
                    'user_id' => $appointment->user_id,
                    'reviewable_type' => Service::class,
                    'reviewable_id' => $appointment->service_id,
                    'rating' => rand(4, 5), // Avaliações positivas (4-5 estrelas)
                    'comment' => $this->getRandomServiceComment(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "Avaliações criadas com sucesso!\n";
    }

    /**
     * Obter comentário aleatório para produtos
     */
    private function getRandomProductComment()
    {
        $comments = [
            'Produto de excelente qualidade! Meu pet adorou.',
            'Chegou rápido e bem embalado. Recomendo!',
            'Ótimo custo-benefício. Voltarei a comprar.',
            'Produto conforme descrito. Muito satisfeito!',
            'Meu pet se adaptou muito bem. Produto de qualidade.',
            'Entrega rápida e produto em perfeitas condições.',
            'Superou minhas expectativas! Produto top.',
            'Recomendo para todos os donos de pets.',
        ];

        return $comments[array_rand($comments)];
    }

    /**
     * Obter comentário aleatório para serviços
     */
    private function getRandomServiceComment()
    {
        $comments = [
            'Serviço impecável! Meu pet ficou lindo.',
            'Profissionais muito atenciosos e carinhosos.',
            'Ótimo atendimento e resultado excepcional.',
            'Meu pet se sentiu muito à vontade. Recomendo!',
            'Serviço de alta qualidade. Voltarei sempre.',
            'Profissional muito competente e cuidadoso.',
            'Resultado perfeito! Meu pet adorou o banho.',
            'Atendimento excelente do início ao fim.',
        ];

        return $comments[array_rand($comments)];
    }
}