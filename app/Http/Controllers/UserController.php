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
        $loggedInUser = auth()->user();
        if ($loggedInUser->role != 'teacher') {
            return ['Usuário sem permissão para esta ação'];
        }

        $users = $user::with(['presences' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->where('role', 'student')
            ->orderBy('name')
            ->get();

        return $users;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $loggedInUser = auth()->user();

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
    public function show(User $user)
    {
        $loggedInUser = auth()->user();
    
        $userWithAddress = User::with('address')->find($user->id);
        return $userWithAddress;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $loggedInUser = auth()->user();

        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $addressData = [
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'number' => $request->number,
            'neighborhood' => $request->neighborhood
        ];

        $user->save();

        $address = $user->address;

        if(isset($address)){
            $address->update($addressData);
        } else{
            $user->address()->create($addressData);
        }

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
