<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{
    public function getModel()
    {
        return Category::class;
    }

    public function get($id)
    {
        $category = Category::all()->firstWhere('id', $id);
        if ($category) {
            return $category;
        }
        return null;
    }

    public function getAll()
    {
        $categories = Category::all();
        if ($categories->isNotEmpty()) {
            return $categories;
        }
        return null;
    }

    public function getChild($id)
    {
        $categories = Category::all()->where('parent_id', $id);
        if ($categories) {
            return $categories;
        }
        return null;
    }

    public function getParent()
    {
        $categories = Category::all()->where('parent_id', NULL);
        if ($categories) {
            return $categories;
        }
        return null;
    }

    public function create(array $data)
    {
        try {
            $existCategory = Category::where('name', '=', $data["name"])->get();
            $categoryObject = new Category();
            $categoryObject->fill($data);

            if ($existCategory->count() == 0) {
                if ($categoryObject->save()) {
                    return $categoryObject;
                }
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function update($category, array $data)
    {
        try {
            if ($category) {
                $category->fill($data);
                if ($category->save()) {
                    return $category;
                }
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function delete($category)
    {
        try {
            if ($category) {
                return $category->delete();
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
