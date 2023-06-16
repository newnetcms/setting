<?php

namespace Newnet\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Newnet\Media\Models\Media;
use Newnet\Setting\Contracts\SettingInterface;
use Newnet\Setting\Repositories\SettingRepository;
use Newnet\Setting\Repositories\SettingRepositoryInterface;
use Illuminate\Support\Arr;

class Setting implements SettingInterface
{
    /**
     * @var SettingRepositoryInterface|SettingRepository
     */
    protected $settingRepository;

    /**
     * Whether the settings data are loaded.
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Settings data
     * @var array
     */
    protected $data = [];

    public function __construct(
        SettingRepositoryInterface $settingRepository,
    ) {
        $this->settingRepository = $settingRepository;
    }

    public function all()
    {
        $this->load();

        return $this->data;
    }

    public function has($name)
    {
        $this->load();

        return Arr::has($this->data, $name);
    }

    public function get($name, $default = null)
    {
        $this->load();

        return Arr::get($this->data, $name) ?: $default;
    }

    public function set($name, $value)
    {
        $this->load();

        $type = gettype($value);
        switch ($type) {
            case 'array':
                $value = json_encode($value);
                break;

            case 'object':
                if ($value instanceof Media) {
                    $media = $value;
                }

                $value = serialize($value);
                break;
        }

        if ($this->has($name)) {
            $setting = $this->settingRepository->updateByName([
                'value' => $value,
                'type'  => $type,
            ], $name);
        } else {
            $setting = $this->settingRepository->create([
                'name'  => $name,
                'value' => $value,
                'type'  => $type,
            ]);
        }

        if (isset($media)) {
            $setting->clearMediaGroup();
            $setting->attachMedia($media);
        }

        return $setting;
    }

    public function forget($name)
    {
        if ($this->has($name)) {
            $this->settingRepository->deleteByName($name);

            Arr::forget($this->data, $name);
        }
    }

    public function load($force = false)
    {
        if (!$this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }
    }

    protected function read()
    {
        if ($this->checkDatabaseConnection()) {
            return $this->parseReadData($this->settingRepository->getAll());
        }

        return [];
    }

    protected function parseReadData($data)
    {
        $results = [];

        foreach ($data as $row) {
            $name = $row->name;
            $value = $row->value;
            $type = $row->type;

            switch ($type) {
                case 'array':
                    $value = json_decode($value, true);
                    break;

                case 'object':
                    $value = unserialize($value);
                    break;

                case 'boolean':
                    $value = (boolean) $value;
                    break;

                case 'integer':
                    $value = (integer) $value;
                    break;

                case 'double':
                    $value = (double) $value;
                    break;
            }

            Arr::set($results, $name, $value);
        }

        return $results;
    }

    private function checkDatabaseConnection()
    {
            try {
                return DB::connection()->getPdo() && Schema::hasTable('settings');
            } catch (\Exception $e) {
                return false;
            }
    }
}
