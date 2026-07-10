<?php

namespace Navia\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Navia\Facades\Navia;

class UserDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = Navia::model('User')->count();
        $string = trans_choice('navia::dimmer.user', $count);

        return view('navia::dimmer', array_merge($this->config, [
            'icon'   => 'navia-group',
            'title'  => "{$count} {$string}",
            'text'   => __('navia::dimmer.user_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('navia::dimmer.user_link_text'),
                'link' => route('navia.users.index'),
            ],
            'image' => navia_asset('images/widget-backgrounds/01.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Navia::model('User'));
    }
}
