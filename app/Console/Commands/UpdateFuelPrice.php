<?php

namespace App\Console\Commands;

use App\Http\Controllers\UpdatefuelController;
use Illuminate\Console\Command;

class UpdateFuelPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fuel:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ราคาน้ำมันอัตโนมัติ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new UpdatefuelController();
        $result = $controller->index();
        $this->info($result);
    }
}
