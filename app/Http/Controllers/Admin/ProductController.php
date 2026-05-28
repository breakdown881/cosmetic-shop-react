<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
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
        $productService = new ProductService($this->productRepository);
        $products = $productService->getAll();
        return view('admin.product.index', [
            'products'    => $products,
            'currentMenu'   => 'products'
        ]);
    }

    public function list($id)
    {
        // $productService    = new ProductService($this->productRepository);
        // $products         = $productService->getChild($id);
        // $product           = $productService->get($id);
        // return view('admin.product.list', [
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
        return view('admin.product.create', ['currentMenu' => 'products']);
    }

    public function createChild($id)
    {
        $productService    = new ProductService($this->productRepository);
        $product           = $productService->get($id);
        return view('admin.product.create', [
            'product'      => $product,
            'isChild'       => true,
            'currentMenu'   => 'products'
        ]);
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
        return view('admin.product.edit', [
            'product'      => $product,
            'currentMenu'   => 'products'
        ]);
    }

    public function editChild($id, Product $product)
    {
        $productService = new ProductService($this->productRepository);
        $parent = $productService->get($id);
        return view('admin.product.edit', [
            'product'      => $product,
            'parent'        => $parent,
            'isChild'       => true,
            'currentMenu'   => 'products'
        ]);
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
