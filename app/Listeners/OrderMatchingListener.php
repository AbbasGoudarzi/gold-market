<?php

namespace App\Listeners;

use App\Events\NewOrderCreated;
use App\Jobs\ProcessOrderMatching;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderMatchingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewOrderCreated $event): void
    {
        ProcessOrderMatching::dispatch();
    }
}
