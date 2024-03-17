<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presence extends Model
{
    use HasFactory;
    protected $fillable = ['status', 'user_id'];

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

    public static function verifyHour($hours){
        $currentHour = Carbon::now()->format('H');
        $foundHour = false;

        foreach ($hours as $hour) {
            $hourParts = explode(':', $hour);
            $hourValue = $hourParts[0];


            if($currentHour >= $hourValue && $currentHour <= $hourValue+3){
                $foundHour = true;
            }
        }

        return $foundHour;
    }
    
}
