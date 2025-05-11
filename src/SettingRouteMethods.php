<?php

namespace Newnet\Setting;

/**
 * @mixin \Illuminate\Routing\Route
 */
class SettingRouteMethods
{
    public function setting()
    {
        return function ($options = []) {
            $this->get('setting', 'SettingController@index')->name('setting.index');
            $this->post('setting/save', 'SettingController@save')->name('setting.save');
        };
    }
}
