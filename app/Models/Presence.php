<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presence extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function verifyPresence($userId){
        $today = Carbon::today()->toDateString();

        $hasPresenceToday = self::where('user_id', $userId)->whereDate('created_at', $today)->get();
    
        return [
            'hasPresenceToday' => $hasPresenceToday,
        ];
    }
    
}
