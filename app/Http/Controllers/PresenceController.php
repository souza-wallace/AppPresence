<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presence;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresenceController extends Controller
{
    public $hours = ["6:00","8:30","10:30","16:30","17:30","20:00"];
    public $confirmed = 'confirmed';
    public $refused = 'refused';
    public $pending = 'pending';

    public function presenceRequest(User $user, Presence $presence){
        $user = auth()->user();

        $result = Presence::verifyPresence($user->id);
        $hasPresenceToday = $result['hasPresenceToday'];
        // return count($hasPresenceToday);

        if(count($hasPresenceToday) != 0){
            return 'Ja pediu presença na data '.now()->format('Y-m-d');
        }

        $result = Presence::verifyHour($this->hours);
        if(!$result){
            return 'Fora do horário de aula';
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

    public function confirm(Presence $presence, Request $request){
        $user = auth()->user();

        if($user->role != 'teacher'){
            return ['Usuario sem permissáo para realizar essa ação'];
        }

        $presences = Presence::whereIn('id', $request->ids)->get();

        foreach ($presences as $key => $presence) {
            $presence->status = 'confirmed';
            $presence->updated_at = Carbon::now();
            $presence->save();
        }

        return $presence;
    }

    public function refuse(Presence $presence, Request $request){
       
        $user = auth()->user();

        if($user->role != 'teacher'){
            return ['Usuario sem permissáo para realizar essa ação'];
        }

        $presences = Presence::whereIn('id', $request->ids)->get();

        foreach ($presences as $key => $presence) {
            $presence->status = 'confirmed';
            $presence->updated_at = Carbon::now();
            $presence->save();
        }

        return $presence;
    }

    public function historic(Request $request, Presence $presence)
    {
        $user = auth()->user();

        $presencesQuery = $presence::with('user')->where('status', '!=', 'pending');
    
        if ($request->has('month')) {
            $month = $request->input('month');
            if ($month != 'all') {
                $presencesQuery->whereMonth('created_at', $month);
            }
        }
    
        if ($request->has('name')) {
            $name = $request->input('name');
            $presencesQuery->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
        }
    
        $presences = $presencesQuery->orderBy('created_at')->get();

        if ($user->role == 'student') {
            $userWithPresences = $user->load('presences');
            $presences = $userWithPresences->presences;
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
