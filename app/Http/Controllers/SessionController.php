<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;


class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Session $session)
    {
        $loggedInUser = auth()->user();

        if($loggedInUser->role != 'teacher'){
            return ['Sem permissão'];
        }

        $session->hour = $request->hour;
        $session->day = $request->day;
        $session->save();

        return $session;

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
