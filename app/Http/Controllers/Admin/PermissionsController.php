<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(){
        $permissions = Permissions::query()
        ->when(request('query'), function($query, $searchQuery){
            $query->where('title', 'like', "%{$searchQuery}%");
        })
        ->latest()
        ->paginate();
        return $permissions;
    }

    public function store(){
        request()->validate([
            'title' => 'required',
        ]);
        return Permissions::create([  
            'title' => request('title'),
        ]);

    }

    public function update(Permissions $permission){

        request()->validate([
            'title' => 'required',
        ]);

        $permission->update([
            'title' => request('title'),
        ]);

        return $permission;

    }

    public function destroy(Permissions $permission){
        $permission->delete();
        return response()->noContent();

    }

    public function bulkDelete(){
        Permissions::whereIn('id', request('ids'))->delete();
        return response()->json(['message', 'Permissions Deleted Successfully']);
    }
}
