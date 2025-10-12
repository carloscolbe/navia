<?php

namespace Navia\Listeners;

use Navia\Events\BreadDeleted;
use Navia\Facades\Navia;

class DeleteBreadMenuItem
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
     * Delete a MenuItem for a given BREAD.
     *
     * @param BreadDeleted $bread
     *
     * @return void
     */
    public function handle(BreadDeleted $bread)
    {
        if (config('navia.bread.add_menu_item')) {
            $menuItem = Navia::model('MenuItem')->where('route', 'navia.'.$bread->dataType->slug.'.index');

            if ($menuItem->exists()) {
                $menuItem->delete();
            }
        }
    }
}
