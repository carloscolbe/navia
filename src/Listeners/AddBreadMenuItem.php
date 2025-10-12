<?php

namespace Navia\Listeners;

use Navia\Events\BreadAdded;
use Navia\Facades\Navia;

class AddBreadMenuItem
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
     * Create a MenuItem for a given BREAD.
     *
     * @param BreadAdded $event
     *
     * @return void
     */
    public function handle(BreadAdded $bread)
    {
        if (config('navia.bread.add_menu_item') && file_exists(base_path('routes/web.php'))) {
            $menu = Navia::model('Menu')->where('name', config('navia.bread.default_menu'))->firstOrFail();

            $menuItem = Navia::model('MenuItem')->firstOrNew([
                'menu_id' => $menu->id,
                'title'   => $bread->dataType->getTranslatedAttribute('display_name_plural'),
                'url'     => '',
                'route'   => 'navia.'.$bread->dataType->slug.'.index',
            ]);

            $order = Navia::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target'     => '_self',
                    'icon_class' => $bread->dataType->icon,
                    'color'      => null,
                    'parent_id'  => null,
                    'order'      => $order,
                ])->save();
            }
        }
    }
}
