<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use App\Services\Admin\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brandService = new BrandService($this->brandRepository);
        $brands = $brandService->getAll();
        return view('admin.brand.index', [
            'brands'        => $brands,
            'currentMenu'   => 'brands'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.brand.create', ['currentMenu' => 'brands']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CreateBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBrandRequest $request)
    {
        try {
            $data = $request->validated();
            $brandService = new BrandService($this->brandRepository);
            $brandService->store($data, $request->file('image'));
            return redirect()->route('admin.brand.index')->with('success', __('translate.createSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.brand.index')->with('success', __('translate.error'));
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
        $brandService = new BrandService($this->brandRepository);
        $brand = $brandService->get($id);
        return view('admin.brand.edit', [
            'brand'         => $brand,
            'currentMenu'   => 'brands'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\CreateBrandRequest  $request
     * @param  Brand $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateBrandRequest $request, Brand $brand)
    {
        try {
            $data = $request->validated();
            $brandService = new BrandService($this->brandRepository);
            $brandService->update($brand, $data, $request->file('image'));
            return redirect()->route('admin.brand.index')->with('success', __('translate.updateSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.brand.index')->with('success', __('translate.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        try {
            $brandService = new BrandService($this->brandRepository);
            $brandService->destroy($brand);
            return redirect()->route('admin.brand.index')->with('success', __('translate.deleteSuccess'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.brand.index')->with('success', __('translate.error'));
        }
    }

    public function changeStatus(Brand $brand, Request $request)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|integer|max:2',
            ]);
            $brandService = new BrandService($this->brandRepository);
            $brandService->changeStatus($brand, $validated['status']);
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
