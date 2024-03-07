<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SideBarController extends Controller
{

    public function index(){

        $getPermissions = User::query()
        ->when(request('query'), function($query, $searchQuery){
            $query->where('name', 'like', "%{$searchQuery}%");
        })
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('users.id', '=', auth()->user()->id)
            ->select('roles.*')
            ->paginate();
            return $getPermissions;
    }
}
