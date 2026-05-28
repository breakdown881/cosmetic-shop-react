<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\Admin\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'categories',
                'apiUrl' => route('admin.api.categories.index'),
                'title' => __('translate.categories'),
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.categories')],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => __('translate.name')],
                    ['key' => 'parent_id', 'label' => 'Parent'],
                    ['key' => 'status', 'label' => __('translate.status'), 'type' => 'boolean'],
                    ['key' => 'created_at', 'label' => __('translate.createdAt')],
                    ['key' => 'updated_at', 'label' => __('translate.updatedAt')],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => __('translate.name'), 'type' => 'text', 'required' => true],
                    [
                        'name' => 'parent_id',
                        'label' => 'Parent',
                        'type' => 'select',
                        'options' => Category::query()
                            ->orderBy('name')
                            ->get(['id', 'name'])
                            ->map(fn (Category $category) => ['value' => $category->id, 'label' => $category->name])
                            ->values()
                            ->all(),
                    ],
                    [
                        'name' => 'status',
                        'label' => __('translate.status'),
                        'type' => 'select',
                        'required' => true,
                        'defaultValue' => 1,
                        'options' => [
                            ['value' => 1, 'label' => __('translate.active')],
                            ['value' => 0, 'label' => __('translate.inactive')],
                        ],
                    ],
                ],
                'labels' => $this->labels(),
        ], 'categories', 'Categories');
    }

    private function labels(): array
    {
        return [
            'add' => __('translate.add'),
            'edit' => __('translate.edit'),
            'delete' => __('translate.delete'),
            'save' => __('translate.save'),
            'cancel' => __('translate.cancel'),
            'management' => __('translate.management'),
            'empty' => __('translate.noData') === 'translate.noData' ? 'Không có dữ liệu.' : __('translate.noData'),
            'deleteConfirm' => __('translate.deleteConfirm') === 'translate.deleteConfirm' ? 'Bạn có chắc muốn xóa?' : __('translate.deleteConfirm'),
        ];
    }

    public function list(int $id)
    {
        $categoryService    = new CategoryService($this->categoryRepository);
        $categories         = $categoryService->getChild($id);
        $category           = $categoryService->get($id);
        return $this->index();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->index();
    }

    public function createChild(int $id)
    {
        $categoryService    = new CategoryService($this->categoryRepository);
        $category           = $categoryService->get($id);
        return $this->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CreateCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $categoryService = new CategoryService($this->categoryRepository);
            $categoryService->store($data);
            return redirect()->route('admin.category.index')->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.category.index')->with('success', __('translate.error'));
        }
    }

    public function storeChild(int $id, CreateCategoryRequest $request)
    {
        try {
            $data               = $request->validated();
            $data['parent_id']  = $id;
            $categoryService    = new CategoryService($this->categoryRepository);
            $categoryService->store($data);
            return redirect()->route('admin.category.list', ['id' => $id])->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.category.list', ['id' => $id])->with('success', __('translate.error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $categoryService = new CategoryService($this->categoryRepository);
        $category = $categoryService->get($id);
        return $this->index();
    }

    public function editChild(int $id, Category $category)
    {
        $categoryService = new CategoryService($this->categoryRepository);
        $parent = $categoryService->get($id);
        return $this->index();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\CreateCategoryRequest  $request
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(CreateCategoryRequest $request, Category $category)
    {
        try {
            $data = $request->validated();
            $categoryService = new CategoryService($this->categoryRepository);
            $categoryService->update($category, $data);
            return redirect()->route('admin.category.index')->with('success', __('translate.updateSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.category.index')->with('success', __('translate.error'));
        }
    }

    public function updateChild(CreateCategoryRequest $request, int $id, Category $category)
    {
        try {
            $data = $request->validated();
            $categoryService = new CategoryService($this->categoryRepository);
            $categoryService->update($category, $data);
            return redirect()->route('admin.category.list', ['id' => $id])->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.category.list', ['id' => $id])->with('success', __('translate.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        try {
            $categoryService = new CategoryService($this->categoryRepository);
            $categoryService->destroy($category);
            return redirect()->back()->with('success', __('translate.deleteSuccess'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('success', __('translate.error'));
        }
    }

    public function changeStatus(Category $category, Request $request)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|integer|max:2',
            ]);
            $categoryService = new CategoryService($this->categoryRepository);
            $categoryService->changeStatus($category, $validated['status']);
            return response()->json([
                'success' => true,
                'message' => __('translate.changeStatusSuccess')
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => __('translate.error')
            ]);
        }
    }
}
