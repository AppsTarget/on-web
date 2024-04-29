<?php
 
namespace App\Console;
 
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\ZEnvia;
 
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $data = new ZEnvia;
            $data->id_agendamento = 1234; 
            $data->text = 'testando'; 
            $data->direction = 'TES'; 
            $data->celular = '123123123'; 
            $data->selected = '1'; 
            $data->save();
        })->everyMinute();
    }
}