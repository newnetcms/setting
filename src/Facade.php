<?php

namespace Newnet\Setting;

use Illuminate\Support\Facades\Facade as BaseFacade;
use Newnet\Setting\Contracts\SettingInterface;

class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor()
    {
        return SettingInterface::class;
    }
}
