<?php

namespace Navia\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Navia\Facades\Navia;

class PageDimmer extends BaseDimmer
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
        $count = Navia::model('Page')->count();
        $string = trans_choice('navia::dimmer.page', $count);

        return view('navia::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-file-text',
            'title'  => "{$count} {$string}",
            'text'   => __('navia::dimmer.page_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('navia::dimmer.page_link_text'),
                'link' => route('navia.pages.index'),
            ],
            'image' => navia_asset('images/widget-backgrounds/03.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Navia::model('Page'));
    }
}
