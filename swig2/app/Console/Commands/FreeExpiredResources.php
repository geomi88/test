<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class FreeExpiredResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FreeExpiredResources:freeresources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Free Expired Resources';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentDate = date('Y-m-d');
            
        $freeallocation = DB::table('resource_allocation')
              ->whereRaw("date(to_date)<'$currentDate' AND resource_type IN('CASHIER','BARISTA')")
              ->update(['active' => 0]);
    }
}
