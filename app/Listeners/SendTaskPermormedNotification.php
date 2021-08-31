<?php

namespace App\Listeners;

use App\Events\TaskPerformed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskPermormedNotification
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
     * @param  TaskPerformed  $event
     * @return void
     */
    public function handle(TaskPerformed $event)
    {
        //
    }
}
