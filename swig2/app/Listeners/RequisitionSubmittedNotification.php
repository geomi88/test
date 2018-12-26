<?php

namespace App\Listeners;
use Kamaln7\Toastr\Facades\Toastr;
use App\Events\RequisitionSubmition;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;

class RequisitionSubmittedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RequisitionSubmition  $event
     * @return void
     */
    public function handle()
    {
       
    }
}
