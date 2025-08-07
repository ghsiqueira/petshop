<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:employee');
    }

    public function dashboard()
    {
        // Redirecionar para o novo dashboard analytics
        return redirect()->route('analytics.employee');
    }

    public function appointments(Request $request)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'Funcionário não encontrado.');
        }

        $query = Appointment::where('employee_id', $employee->id)
            ->with(['pet', 'service', 'user']);

        // Aplicar filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointment_datetime', '<=', $request->date_to);
        }

        // Se não há filtro de data, mostrar apenas agendamentos futuros e recentes (últimos 30 dias)
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->where('appointment_datetime', '>=', now()->subDays(30));
        }

        $appointments = $query->orderBy('appointment_datetime', 'desc')->paginate(15);

        return view('employee.appointments', compact('appointments'));
    }
}