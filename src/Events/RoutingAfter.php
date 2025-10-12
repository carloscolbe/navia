<?php

namespace Navia\Events;

use Illuminate\Queue\SerializesModels;

class RoutingAfter
{
    use SerializesModels;

    public $router;

    public function __construct()
    {
        $this->router = app('router');

        // @deprecate
        //
        event('navia.routing.after', $this->router);
    }
}
