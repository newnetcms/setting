<?php

use Newnet\Setting\Contracts\SettingInterface;

if (!function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     * If an array is passed as the key, we will assume you want to set an array of values.
     * @param array|string $name
     * @param mixed        $default
     * @return mixed
     */
    function setting($name = null, $default = null)
    {
        /** @var SettingInterface $setting */
        $setting = app(SettingInterface::class);

        if (is_null($name)) {
            return $setting;
        }

        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $setting->set($key, $value);
            }

            return $setting;
        }

        return $setting->get($name, $default);
    }
}
