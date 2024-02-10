<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presence;
use Illuminate\Support\Facades\DB;

class PresenceController extends Controller
{

    public $confirmed = 'confirmed';
    public $recused = 'recused';

    public function presenceRequest(User $user, Presence $presence){
        $user = auth()->user();

        $result = Presence::verifyPresence($user->id);
        $hasPresenceToday = $result['hasPresenceToday'];

        //VERIFICAR NAO ESTA FUNCIONANDO
        // if(!$hasPresenceToday){
        //     return response()->json(['Ja pediu presença na data '.now()->format('Y-m-d')], 405);
        // }

        $presence->user_id = $user->id;
        $presence->status = 'pending';
        $presence->save();

        return $presence;
    }

    public function pending(Presence $presence){
        $user = auth()->user();

        if($user->role != 'teatcher'){
            return response()->json(['Usuario sem permissáo para realizar essa ação'], 405);
        }

        return $presence::where('status', 'pending')->orderBy('created_at', 'asc')->get();

    }

    public function confirm(Presence $presence){
        $user = auth()->user();

        if($user->role != 'teatcher'){
            return response()->json(['Usuario sem permissáo para realizar essa ação'], 405);
        }

        $presence->status = 'confirmed';
        $presence->updated_at = now();
        return $presence;
    }

    public function refuse(Presence $presence){
       
        $user = auth()->user();

        if($user->role != 'teatcher'){
            return response()->json(['Usuario sem permissáo para realizar essa ação'], 405);
        }

        $presence->status = 'refused';
        $presence->updated_at = now();
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
        $status = 'confirmed';

        $presences = $presence::where('status', $this->confirmed)->where('user_id', $user->id)->get();
        $fouls = $presence::where('status', $this->recused)->where('user_id', $user->id)->get();

        return response()->json([
            "countPresence" => count($presences),
            "countFouls" => count($fouls)
        ], 200);

    }
    


}
