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
    
    public function appointments()
    {
        $employee = auth()->user()->employee;
        
        $today = now()->format('Y-m-d');
        $appointments = Appointment::where('employee_id', $employee->id)
                                 ->whereDate('appointment_datetime', '>=', $today)
                                 ->with(['pet', 'service', 'user'])
                                 ->orderBy('appointment_datetime')
                                 ->paginate(10);
        
        return view('employee.appointments', compact('appointments'));
    }
    
    public function dashboard()
    {
        $employee = auth()->user()->employee;
        
        $todayAppointments = Appointment::where('employee_id', $employee->id)
                                      ->whereDate('appointment_datetime', now()->format('Y-m-d'))
                                      ->with(['pet', 'service', 'user'])
                                      ->orderBy('appointment_datetime')
                                      ->get();
        
        $upcomingAppointments = Appointment::where('employee_id', $employee->id)
                                         ->whereDate('appointment_datetime', '>', now()->format('Y-m-d'))
                                         ->where('status', '!=', 'cancelled')
                                         ->orderBy('appointment_datetime')
                                         ->take(5)
                                         ->get();
        
        return view('employee.dashboard', compact('employee', 'todayAppointments', 'upcomingAppointments'));
    }
}