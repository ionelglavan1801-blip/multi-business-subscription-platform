<?php

namespace App\Listeners;

use App\Actions\Business\CreateFirstBusiness;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserFirstBusiness implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $action = new CreateFirstBusiness;
        $action->execute($event->user);
    }
}
