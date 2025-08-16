<?php

namespace App\Http\Controllers\Petshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBusinessHoursRequest;
use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BusinessHoursController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware apenas para métodos específicos, excluindo getAvailableSlots
        $this->middleware('auth')->except(['getAvailableSlots']);
        $this->middleware('role:petshop')->except(['getAvailableSlots']);
    }

    /**
     * Mostrar página de configuração de horários
     */
    public function index()
    {
        $petshop = auth()->user()->petshop;
        
        if (!$petshop) {
            return redirect()->route('dashboard')->with('error', 'Pet shop não encontrado.');
        }

        $businessHours = $petshop->getBusinessHours();

        return view('petshop.business-hours.index', compact('petshop', 'businessHours'));
    }

    /**
     * Atualizar horários de funcionamento
     */
    public function update(UpdateBusinessHoursRequest $request)
    {
        $petshop = auth()->user()->petshop;
        
        if (!$petshop) {
            return redirect()->route('dashboard')->with('error', 'Pet shop não encontrado.');
        }
        
        $businessHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($days as $day) {
            $businessHours[$day] = [
                'enabled' => $request->has($day . '_enabled'),
                'open' => $request->input($day . '_open', '08:00'),
                'close' => $request->input($day . '_close', '18:00'),
            ];
        }

        $petshop->update([
            'business_hours' => $businessHours,
            'slot_duration' => $request->slot_duration,
            'advance_booking_days' => $request->advance_booking_days,
            'allow_weekend_booking' => $request->has('allow_weekend_booking'),
            'lunch_break_start' => $request->lunch_break_start,
            'lunch_break_end' => $request->lunch_break_end,
        ]);

        return redirect()->route('petshop.business-hours.index')
            ->with('success', 'Horários de funcionamento atualizados com sucesso!');
    }

    /**
     * API para obter horários disponíveis para agendamento
     * MÉTODO PÚBLICO - SEM AUTENTICAÇÃO REQUERIDA
     */
    public function getAvailableSlots(Request $request, $petshopId)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:services,id',
                'date' => 'required|date_format:Y-m-d',
            ]);

            $petshop = \App\Models\Petshop::findOrFail($petshopId);
            $service = Service::where('id', $request->service_id)
                             ->where('petshop_id', $petshop->id)
                             ->firstOrFail();

            $date = $request->input('date');
            $slots = $this->generateAvailableSlots($petshop, $service, $date);

            return response()->json([
                'success' => true,
                'slots' => $slots,
                'date' => $date,
                'service' => $service->name,
                'petshop' => $petshop->name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar horários disponíveis.',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Gerar slots disponíveis para uma data específica
     */
    private function generateAvailableSlots($petshop, $service, $date)
    {
        try {
            $dateObj = Carbon::createFromFormat('Y-m-d', $date);
            
            // Verificar se a data não é muito distante
            $maxDate = Carbon::now()->addDays($petshop->advance_booking_days ?? 30);
            if ($dateObj->gt($maxDate)) {
                return [];
            }

            // Verificar se não é no passado
            if ($dateObj->lt(Carbon::today())) {
                return [];
            }

            $dayOfWeek = strtolower($dateObj->format('l')); // monday, tuesday, etc.
            
            // Verificar se o serviço está disponível neste dia
            if (!$service->isAvailableOnDay($dayOfWeek)) {
                return [];
            }

            // Obter horários de funcionamento
            $businessHours = $petshop->getBusinessHours();
            
            if (!isset($businessHours[$dayOfWeek]) || !$businessHours[$dayOfWeek]['enabled']) {
                return []; // Fechado neste dia
            }

            $dayHours = $businessHours[$dayOfWeek];
            $startTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $dayHours['open']);
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $dayHours['close']);
            
            // CORREÇÃO: Usar apenas slot_duration para intervalos, não somar buffer_time
            $slotDuration = $petshop->slot_duration ?? 30; // Intervalo entre slots disponíveis
            $serviceDuration = $service->duration_minutes ?? 30; // Duração real do serviço
            $bufferTime = $service->buffer_time ?? 0; // Tempo extra após o serviço
            
            // Duração total que o serviço ocupa (para verificar conflitos)
            $totalServiceTime = $serviceDuration + $bufferTime;

            $slots = [];
            $currentTime = $startTime->copy();

            // Se for hoje, começar a partir do horário atual + 1 hora
            if ($dateObj->isToday()) {
                $minTime = Carbon::now()->addHour()->roundUpToNearestMinutes($slotDuration);
                if ($currentTime->lt($minTime)) {
                    $currentTime = $minTime->copy();
                }
            }

            while ($currentTime->copy()->addMinutes($totalServiceTime)->lte($endTime)) {
                $timeString = $currentTime->format('H:i');
                
                // Verificar se não está no horário de almoço
                if (!$this->isInLunchBreak($currentTime, $petshop)) {
                    // Verificar se o slot não está ocupado
                    if (!$this->isSlotOccupied($currentTime, $service, $totalServiceTime)) {
                        $slots[] = $timeString;
                    }
                }
                
                // CORREÇÃO: Avançar apenas pelo slot_duration, não pelo tempo total do serviço
                $currentTime->addMinutes($slotDuration);
            }

            return $slots;

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar slots disponíveis: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar se está no horário de almoço
     */
    private function isInLunchBreak($time, $petshop)
    {
        if (!$petshop->lunch_break_start || !$petshop->lunch_break_end) {
            return false;
        }

        try {
            $lunchStart = Carbon::createFromFormat('H:i', $petshop->lunch_break_start);
            $lunchEnd = Carbon::createFromFormat('H:i', $petshop->lunch_break_end);
            $timeOnly = Carbon::createFromFormat('H:i', $time->format('H:i'));

            return $timeOnly->between($lunchStart, $lunchEnd);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar se o slot está ocupado
     */
    private function isSlotOccupied($startTime, $service, $duration)
    {
        $endTime = $startTime->copy()->addMinutes($duration);

        return Appointment::where('service_id', $service->id)
            ->whereDate('appointment_datetime', $startTime->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startTime, $endTime) {
                // Verificar sobreposição de horários
                $query->where(function ($q) use ($startTime, $endTime) {
                    // Agendamento começar durante nosso slot
                    $q->whereBetween('appointment_datetime', [$startTime, $endTime->subMinute()]);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // Nosso slot começar durante um agendamento existente
                    $q->where('appointment_datetime', '<=', $startTime)
                      ->whereRaw('DATE_ADD(appointment_datetime, INTERVAL (SELECT duration_minutes + COALESCE(buffer_time, 0) FROM services WHERE id = appointments.service_id) MINUTE) > ?', [$startTime]);
                });
            })
            ->exists();
    }

    /**
     * API para testar configuração de horários
     */
    public function testConfiguration(Request $request)
    {
        $petshop = auth()->user()->petshop;
        
        if (!$petshop) {
            return response()->json(['error' => 'Pet shop não encontrado'], 404);
        }

        $testDate = $request->input('date', Carbon::tomorrow()->format('Y-m-d'));
        $services = $petshop->services()->active()->get();
        
        $result = [];
        
        foreach ($services as $service) {
            $slots = $this->generateAvailableSlots($petshop, $service, $testDate);
            $result[] = [
                'service_id' => $service->id,
                'service_name' => $service->name,
                'available_slots' => count($slots),
                'slots' => $slots
            ];
        }

        return response()->json([
            'date' => $testDate,
            'petshop' => $petshop->name,
            'services' => $result
        ]);
    }
}