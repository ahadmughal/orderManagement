<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(){
        $roles = Roles::query()
        ->when(request('query'), function($query, $searchQuery){
            $query->where('title', 'like', "%{$searchQuery}%");
        })
        ->latest()
        ->paginate();
        return $roles;
    }

    public function store(){
        request()->validate([
            'title' => 'required',
        ]);
        return Roles::create([  
            'title' => request('title'),
            'blogs' => request('blogs'),
            'pages' => request('pages'),
            'submitted_forms' => request('forms'),
            'users' => request('users'),
            'roles' => request('roles'),
            'permissions' => request('permissions'),
            'profile' => request('profile'),
            'products' => request('products'),
        ]);

    }

    public function update(Roles $role){

        request()->validate([
            'title' => 'required',
        ]);

        $role->update([
            'title' => request('title'),
        ]);

        return $role;

    }

    public function updateRolePermissions(Roles $role){

        $role->update([
            'users' => request('users'),
            'blogs' => request('blogs'),
            'profile' => request('profile'),
            'pages' => request('pages'),
            'roles' => request('roles'),
            'submitted_forms' => request('forms'),
            'permissions' => request('permissions'),
            'products' => request('products'),

        ]);

        return $role;

    }

    public function destroy(Roles $role){
        $role->delete();
        return response()->noContent();

    }

    public function bulkDelete(){
        Roles::whereIn('id', request('ids'))->delete();
        return response()->json(['message', 'Permissions Deleted Successfully']);
    }
}
