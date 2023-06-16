<?php

namespace Newnet\Setting\Repositories;

use Illuminate\Database\Eloquent\Model;
use Newnet\Setting\Models\Setting;

class SettingRepository implements SettingRepositoryInterface
{
    /** @var Setting $model */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function findByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function getAll()
    {
        return $this->model->all(['name', 'value', 'type']);
    }

    public function deleteByName($name)
    {
        return $this->model->where('name', $name)->delete();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $condition, array $data)
    {
        return $this->model->where($condition)->update($data);
    }

    public function updateByName(array $data, $name)
    {
        $model = $this->findByName($name);

        $model->update($data);

        return $model;
    }
}
