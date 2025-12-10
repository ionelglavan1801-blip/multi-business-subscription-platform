<?php

namespace App\Listeners;

use App\Actions\Business\CreateFirstBusiness;
use Illuminate\Auth\Events\Registered;

class CreateUserFirstBusiness
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $action = new CreateFirstBusiness;
        $action->execute($event->user);
    }
}
