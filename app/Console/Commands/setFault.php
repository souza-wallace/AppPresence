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
    public function handle(User $user)
    {
        $users = $user::with('presences')->get();
        $currentDate = Carbon::now()->format('Y-m-d');

        foreach ($users as $user) {
            $hasPresenceToday = false;

            foreach ($user->presences as $p) {
                $presenceDate = $p->created_at->format('Y-m-d');

                if ($presenceDate == $currentDate) {
                    $hasPresenceToday = true;
                    break; 
                }
            }

            if (!$hasPresenceToday) {
                $presence = Presence::create([
                    'user_id' => $user->id,
                    'status' => 'refused',
                ]);
                

                Log::info('Criou falta para o usuario: '. $user->id);

            }
        }

        Log::info('Cron executada com sucesso em '. Carbon::now());
    }

}
