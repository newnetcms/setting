<?php

namespace Newnet\Setting\Contracts;

interface SettingInterface
{
    /**
     * Get all config.
     * @return array
     */
    public function all();

    /**
     * Determine if the given configuration value exists.
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * Get the specified configuration value
     * @param string $name
     * @param mixed  $default
     * @return string
     */
    public function get($name, $default = null);

    /**
     * Set a given configuration value.
     * @param string $name
     * @param mixed  $value
     * @return \Newnet\Setting\Models\Setting
     */
    public function set($name, $value);

    /**
     * Remove configuration
     * @param $name
     * @return mixed
     */
    public function forget($name);
}
