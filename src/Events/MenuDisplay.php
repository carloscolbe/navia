<?php

namespace Navia\Events;

use Illuminate\Queue\SerializesModels;
use Navia\Models\Menu;

class MenuDisplay
{
    use SerializesModels;

    public $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;

        // @deprecate
        //
        event('navia.menu.display', $menu);
    }
}
