<?php

namespace Navia\Events;

use Illuminate\Queue\SerializesModels;
use Navia\Models\Setting;

class SettingUpdated
{
    use SerializesModels;

    public $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }
}
