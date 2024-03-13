<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Presence;
use App\Models\User;

use Carbon\Carbon;

class setFault extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-fault';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Presence $presence, User $user)
    {
        $users = $user::with('presences')->get();
        $currentDate = Carbon::now()->format('Y-m-d');

        foreach ($users as $user) {
            $hasPresenceToday = false;

            foreach ($user->presences as $presence) {
                $presenceDate = $presence->created_at->format('Y-m-d');

                if ($presenceDate == $currentDate) {
                    $hasPresenceToday = true;
                    break; 
                }
            }

            if (!$hasPresenceToday) {
                $presence->user_id = $user->id;
                $presence->status = 'refused';
                $presence->save();

                Log::info('Criou falta para o usuario: '. $user->id);

            }
        }

        Log::info('Cron executada com sucesso em '. Carbon::now());
    }

}
