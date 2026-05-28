<?php
namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function get($id);

    public function getAll();

    public function delete(array $ids);
}
