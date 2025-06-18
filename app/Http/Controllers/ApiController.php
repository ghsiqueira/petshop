<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Employee;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getServices($petshopId)
    {
        $services = Service::where('petshop_id', $petshopId)
            ->where('is_active', true)
            ->get();
        
        return response()->json($services);
    }
    
    public function getEmployees($petshopId)
    {
        $employees = Employee::where('petshop_id', $petshopId)
            ->with('user')
            ->get();
        
        return response()->json($employees);
    }
}