<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Mockery\Undefined;

use function Laravel\Prompts\password;

class UsersController extends Controller
{
    public function index(){

        $users = User::query()
        ->when(request('query'), function($query, $searchQuery){
            $query->where('name', 'like', "%{$searchQuery}%");
        })
        ->latest()
        ->paginate();
        return $users;
    }

    public function store(){

        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);

        return User::create([  
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'role' => request('roleAssigned'),
        ]);

    }

    public function update(User $user){

        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:8',
        ]);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
            'role' => request('roleAssigned') ? request('roleAssigned') : $user->role,
        ]);

        return $user;

    }

    public function destroy(User $user){
        
        $user->delete();
        return response()->noContent();

    }

    public function changeRole(User $user){
        $user->update([
            'role' => request('role'),
        ]);

        return response()->json(['success' => true]);
    }

    public function search(){
        $searchQuery = request('query');
        $users = User::where('name', 'like', "%{$searchQuery}%")->paginate();
        return response()->json($users);

    }

    public function bulkDelete(){
        User::whereIn('id', request('ids'))->delete();
        return response()->json(['message', 'Users Deleted Successfully']);
    }
}
