<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Presence;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $user = auth()->user();
        
        if ($user->role != 'teacher') {
            return ['Usuário sem permissão para esta ação'];
        }
        
        $users = $user::with('presences')->where('role', 'student')->orderBy('name')->get();
    
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->belt = $request->belt;
        $user->role = 'student';

        $user->save();
        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
