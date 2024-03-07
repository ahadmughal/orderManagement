<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointments;

class AppoitmentController extends Controller
{
    public function index(){

        return Appointments::query()
        ->with('client:id,first_name,last_name')
        ->latest()
        ->paginate(5);
       
        
    }
}
