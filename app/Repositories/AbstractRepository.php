<?php

namespace App\Repositories;

abstract class AbstractRepository implements BaseRepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    /**
     * Singleton the model
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    public function delete(array $ids)
    {
        // TODO: Implement delete() method.
    }
}
