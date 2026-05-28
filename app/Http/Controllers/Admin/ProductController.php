<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'products',
                'apiUrl' => route('admin.api.products.index'),
                'title' => __('translate.products'),
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.products')],
                ],
                'columns' => [
                    ['key' => 'code', 'label' => __('translate.code')],
                    ['key' => 'name', 'label' => __('translate.name')],
                    ['key' => 'brand_id', 'label' => __('translate.brands')],
                    ['key' => 'category_id', 'label' => __('translate.categories')],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'inventory_qty', 'label' => 'Inventory'],
                    ['key' => 'status', 'label' => __('translate.status'), 'type' => 'boolean'],
                ],
                'fields' => [
                    ['name' => 'code', 'label' => __('translate.code'), 'type' => 'text', 'required' => true],
                    ['name' => 'name', 'label' => __('translate.name'), 'type' => 'text', 'required' => true],
                    [
                        'name' => 'brand_id',
                        'label' => __('translate.brands'),
                        'type' => 'select',
                        'required' => true,
                        'options' => Brand::query()
                            ->orderBy('name')
                            ->get(['id', 'name'])
                            ->map(fn (Brand $brand) => ['value' => $brand->id, 'label' => $brand->name])
                            ->values()
                            ->all(),
                    ],
                    [
                        'name' => 'category_id',
                        'label' => __('translate.categories'),
                        'type' => 'select',
                        'required' => true,
                        'options' => Category::query()
                            ->orderBy('name')
                            ->get(['id', 'name'])
                            ->map(fn (Category $category) => ['value' => $category->id, 'label' => $category->name])
                            ->values()
                            ->all(),
                    ],
                    ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'required' => true, 'defaultValue' => 0],
                    ['name' => 'discount_percentage', 'label' => 'Discount %', 'type' => 'number', 'required' => true, 'defaultValue' => 0],
                    ['name' => 'discount_from_date', 'label' => 'Discount from', 'type' => 'date', 'required' => true],
                    ['name' => 'discount_to_date', 'label' => 'Discount to', 'type' => 'date', 'required' => true],
                    ['name' => 'media_id', 'label' => 'Media ID', 'type' => 'number', 'required' => true, 'defaultValue' => 1],
                    ['name' => 'inventory_qty', 'label' => 'Inventory', 'type' => 'number', 'required' => true, 'defaultValue' => 0],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'text', 'required' => true],
                    ['name' => 'star', 'label' => 'Star', 'type' => 'number', 'required' => true, 'defaultValue' => 0],
                    [
                        'name' => 'featured',
                        'label' => 'Featured',
                        'type' => 'select',
                        'required' => true,
                        'defaultValue' => 0,
                        'options' => [
                            ['value' => 1, 'label' => 'Yes'],
                            ['value' => 0, 'label' => 'No'],
                        ],
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
        ], 'products', 'Products');
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

    public function list($id)
    {
        // $productService    = new ProductService($this->productRepository);
        // $products         = $productService->getChild($id);
        // $product           = $productService->get($id);
        // Legacy product list view was replaced by React API shell.
        //     'products'    => $products,
        //     'product'      => $product,
        //     'currentMenu'   => 'products'
        // ]);
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

    public function createChild($id)
    {
        $productService    = new ProductService($this->productRepository);
        $product           = $productService->get($id);
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
            $productService = new ProductService($this->productRepository);
            $productService->store($data);
            return redirect()->route('admin.product.index')->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.product.index')->with('success', __('translate.error'));
        }
    }

    public function storeChild($id, CreateCategoryRequest $request)
    {
        try {
            $data               = $request->validated();
            $data['parent_id']  = $id;
            $productService    = new ProductService($this->productRepository);
            $productService->store($data);
            return redirect()->route('admin.product.list', ['id' => $id])->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.product.list', ['id' => $id])->with('success', __('translate.error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $productService = new ProductService($this->productRepository);
        $product = $productService->get($id);
        return $this->index();
    }

    public function editChild($id, Product $product)
    {
        $productService = new ProductService($this->productRepository);
        $parent = $productService->get($id);
        return $this->index();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\CreateCategoryRequest  $request
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(CreateCategoryRequest $request, Product $product)
    {
        try {
            $data = $request->validated();
            $productService = new ProductService($this->productRepository);
            $productService->update($product, $data);
            return redirect()->route('admin.product.index')->with('success', __('translate.updateSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.product.index')->with('success', __('translate.error'));
        }
    }

    public function updateChild(CreateCategoryRequest $request, $id, Product $product)
    {
        try {
            $data = $request->validated();
            $productService = new ProductService($this->productRepository);
            $productService->update($product, $data);
            return redirect()->route('admin.product.list', ['id' => $id])->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.product.list', ['id' => $id])->with('success', __('translate.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            $productService = new ProductService($this->productRepository);
            $productService->destroy($product);
            return redirect()->back()->with('success', __('translate.deleteSuccess'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('success', __('translate.error'));
        }
    }

    public function changeStatus(Product $product, Request $request)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|integer|max:2',
            ]);
            $productService = new ProductService($this->productRepository);
            $productService->changeStatus($product, $validated['status']);
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
