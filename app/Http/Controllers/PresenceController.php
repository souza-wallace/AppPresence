<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presence;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresenceController extends Controller
{

    public $confirmed = 'confirmed';
    public $refused = 'refused';
    public $pending = 'pending';

    public function presenceRequest(User $user, Presence $presence){
        $user = auth()->user();

        $result = Presence::verifyPresence($user->id);
        $hasPresenceToday = $result['hasPresenceToday'];

        if(count($hasPresenceToday) != 0){
            return 'Ja pediu presença na data '.now()->format('Y-m-d');
        }

        $presence->user_id = $user->id;
        $presence->status = 'pending';
        $presence->save();

        return $presence;
    }

    public function pending(Presence $presence){
        $user = auth()->user();

        if($user->role != 'teacher'){
            return response()->json(['Usuario sem permissáo para realizar essa ação'], 405);
        }

        return $presence::with('user')->where('status', 'pending')->orderBy('created_at', 'asc')->get();
    }

    public function confirm(Presence $presence){
        $user = auth()->user();

        if($user->role != 'teacher'){
            return ['Usuario sem permissáo para realizar essa ação'];
        }

        $presence->status = 'confirmed';
        $presence->updated_at = Carbon::now();
        $presence->save();

        return $presence;
    }

    public function refuse(Presence $presence){
       
        $user = auth()->user();

        if($user->role != 'teacher'){
            return ['Usuario sem permissáo para realizar essa ação'];
        }

        $presence->status = 'refused';
        $presence->updated_at = Carbon::now();
        $presence->save();

        return $presence;
    }

    public function historic(Request $request, User $user, Presence $presence)
    {
        $user = auth()->user();
        $userWithPresences = $user::with('presences')->get();
        $presences = $userWithPresences[0]->presences;
    
        if ($request->input('month') == 1) {
            $presences = Presence::where('user_id', $user->id)
                ->whereYear('created_at', '=', now()->subMonth()->year)
                ->whereMonth('created_at', '=', now()->subMonth()->month)
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('m');
                });
        }
    
        if ($request->input('day') == 1) {
            $presences = Presence::where('user_id', $user->id)
                ->whereDate('created_at', '=', now()->subDay()->toDateString())
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('d');
                });
        }
    
        return $presences;
    }

    public function presences(Presence $presence){
        $user = auth()->user();

        $presences = [];
        $fouls = [];
        $pendingUser = 0;
        $today = Carbon::today();

        // $pending = $presence::with('user')->where('status', $this->pending)->where('user_id', $user->id)->get();
        // $refused = $presence::with('user')->where('status', $this->refused)->where('user_id', $user->id)->whereDate('created_at', $today)->first();

        return response()->json([
            "pending" => $pending ? $pending : 0,
            "refused" => $refused ? $refused : 0,
        ], 200);

    }

    public function dataUser(Presence $presence){
        $user = auth()->user();

        $presences = [];
        $fouls = [];
        $pending = 0;
        $today = Carbon::today()->toDateString();

        $presences = $presence::where('status', $this->confirmed)->where('user_id', $user->id)->get();
        $fouls = $presence::where('status', $this->refused)->where('user_id', $user->id)->get();
        $pending = $presence::where('status', $this->pending)->where('user_id', $user->id)->whereDate('created_at', $today)->first();

        return response()->json([
            "countPresence" => count($presences),
            "countFouls" => count($fouls),
            "pending" => $pending ? $pending : []
        ], 200);

    }
}
