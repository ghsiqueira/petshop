<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Petshop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $now = Carbon::now();
        
        $upcomingAppointments = Appointment::where('user_id', auth()->id())
                                         ->where('appointment_datetime', '>=', $now)
                                         ->with(['pet', 'service', 'employee.petshop'])
                                         ->orderBy('appointment_datetime')
                                         ->get();
                                         
        $previousAppointments = Appointment::where('user_id', auth()->id())
                                         ->where('appointment_datetime', '<', $now)
                                         ->with(['pet', 'service', 'employee.petshop'])
                                         ->orderBy('appointment_datetime', 'desc')
                                         ->get();
        
        return view('appointments.index', compact('upcomingAppointments', 'previousAppointments'));
    }

    public function create(Request $request)
    {
        $pets = auth()->user()->pets;
        $petshops = Petshop::where('is_active', true)->get();
        $services = collect(); // Será carregado via AJAX baseado no petshop selecionado
        $employees = collect(); // Será carregado via AJAX baseado no serviço selecionado
        
        // Se tiver um service_id na requisição, vamos pré-carregar o serviço com suas avaliações
        $selectedService = null;
        if ($request->has('service_id')) {
            $selectedService = Service::with(['reviews.user', 'petshop'])
                ->findOrFail($request->service_id);
            $services = Service::where('petshop_id', $selectedService->petshop_id)
                ->where('is_active', true)
                ->get();
            $employees = Employee::where('petshop_id', $selectedService->petshop_id)->get();
        }
        
        return view('appointments.create', compact('pets', 'petshops', 'services', 'employees', 'selectedService'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:employees,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Apenas validar o horário se a data for hoje
                    if ($request->appointment_date == date('Y-m-d')) {
                        // Combine date and time
                        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $value);
                        $now = Carbon::now();
                        
                        // Adiciona um pequeno buffer (5 minutos) para evitar problemas com horários muito próximos
                        $now->subMinutes(5);
                        
                        // Check if the combined date/time is in the past
                        if ($appointmentDateTime->isPast() && $appointmentDateTime->lt($now)) {
                            $fail('O horário do agendamento deve ser no futuro.');
                        }
                    }
                    // Se a data for futura, qualquer horário é válido
                }
            ],
        ]);
        
        // Verificar se o pet pertence ao usuário
        $pet = Pet::findOrFail($request->pet_id);
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
        
        $appointment = new Appointment();
        $appointment->user_id = auth()->id();
        $appointment->pet_id = $request->pet_id;
        $appointment->service_id = $request->service_id;
        $appointment->employee_id = $request->employee_id;
        $appointment->appointment_datetime = $appointmentDateTime;
        $appointment->status = 'pending';
        if ($request->has('notes')) {
            $appointment->notes = $request->notes;
        }
        $appointment->save();
        
        return redirect()->route('appointments.index')
                       ->with('success', 'Agendamento criado com sucesso!');
    }

    public function show(Appointment $appointment)
    {
        // Verificar se o usuário atual tem permissão para ver este agendamento
        if ($appointment->user_id !== auth()->id() && 
            !auth()->user()->hasAnyRole(['admin', 'petshop'])) {
            
            // Verificar se é funcionário do petshop relacionado ao agendamento
            $isEmployeeOfSamePetshop = false;
            
            if (auth()->user()->employee) {
                $userEmployeePetshopId = auth()->user()->employee->petshop_id;
                $appointmentPetshopId = $appointment->employee->petshop_id ?? null;
                
                if ($userEmployeePetshopId && $appointmentPetshopId && $userEmployeePetshopId === $appointmentPetshopId) {
                    $isEmployeeOfSamePetshop = true;
                }
            }
            
            if (!$isEmployeeOfSamePetshop) {
                abort(403, 'Você não tem permissão para ver este agendamento.');
            }
        }
        
        // Carregando relações necessárias para a view
        $appointment->load([
            'pet',
            'service.petshop',
            'employee.user',
        ]);
        
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        // Apenas o próprio usuário pode editar seus agendamentos 
        // e apenas se estiverem pendentes
        if ($appointment->user_id !== auth()->id() || $appointment->status !== 'pending') {
            abort(403);
        }
        
        $pets = auth()->user()->pets;
        $petshop = $appointment->employee->petshop;
        
        // Carregando serviço com avaliações
        $service = $appointment->service;
        $service->load(['reviews.user']);
        
        $services = Service::where('petshop_id', $petshop->id)->get();
        $employees = Employee::where('petshop_id', $petshop->id)->get();
        
        return view('appointments.edit', compact('appointment', 'pets', 'services', 'employees'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        // Apenas o próprio usuário pode editar seus agendamentos 
        // e apenas se estiverem pendentes
        if ($appointment->user_id !== auth()->id() || $appointment->status !== 'pending') {
            abort(403);
        }
        
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:employees,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Combine date and time
                    $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $value);
                    
                    // Check if the combined date/time is in the past
                    if ($appointmentDateTime->isPast()) {
                        $fail('O horário do agendamento não pode estar no passado.');
                    }
                }
            ],
        ]);
        
        // Verificar se o pet pertence ao usuário
        $pet = Pet::findOrFail($request->pet_id);
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
        
        $appointment->pet_id = $request->pet_id;
        $appointment->service_id = $request->service_id;
        $appointment->employee_id = $request->employee_id;
        $appointment->appointment_datetime = $appointmentDateTime;
        if ($request->has('notes')) {
            $appointment->notes = $request->notes;
        }
        $appointment->save();
        
        return redirect()->route('appointments.index')
                       ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Appointment $appointment)
    {
        // Apenas o próprio usuário pode cancelar seus agendamentos 
        // e apenas se estiverem pendentes ou confirmados
        if ($appointment->user_id !== auth()->id() || 
            !in_array($appointment->status, ['pending', 'confirmed'])) {
            abort(403);
        }
        
        $appointment->status = 'cancelled';
        $appointment->save();
        
        return redirect()->route('appointments.index')
                       ->with('success', 'Agendamento cancelado com sucesso!');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Apenas funcionários ou petshops podem atualizar o status
        if (!auth()->user()->hasRole('admin') && 
            !auth()->user()->hasRole('petshop') && 
            (!auth()->user()->employee || auth()->user()->employee->petshop_id !== $appointment->employee->petshop_id)) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);
        
        $appointment->status = $request->status;
        $appointment->save();
        
        return back()->with('success', 'Status do agendamento atualizado com sucesso!');
    }
}