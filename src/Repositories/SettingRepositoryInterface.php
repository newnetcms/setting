<?php

namespace Newnet\Setting\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface SettingRepositoryInterface
{
    /**
     * @return Collection|mixed
     */
    public function getAll();

    /**
     * @param $name
     * @return mixed
     */
    public function findByName($name);

    /**
     * @param $name
     * @return mixed
     */
    public function deleteByName($name);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $condition
     * @param array $data
     * @return mixed
     */
    public function update(array $condition, array $data);

    /**
     * @param  array  $data
     * @param $name
     * @return mixed
     */
    public function updateByName(array $data, $name);
}
