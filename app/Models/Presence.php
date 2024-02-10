<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function verifyPresence($userId){
        $presencesUser = self::where('user_id', $userId)->get();
    
        $today = now()->format('Y-m-d');
        $hasPresenceToday = $presencesUser->contains('created_at', '=', $today);
    
        return [
            'hasPresenceToday' => $hasPresenceToday,
        ];
    }
    
}
