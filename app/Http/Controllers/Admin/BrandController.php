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
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'brands',
                'apiUrl' => route('admin.api.brands.index'),
                'title' => __('translate.brands'),
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.brands')],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => __('translate.name')],
                    ['key' => 'status', 'label' => __('translate.status'), 'type' => 'boolean'],
                    ['key' => 'created_at', 'label' => __('translate.createdAt')],
                    ['key' => 'updated_at', 'label' => __('translate.updatedAt')],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => __('translate.name'), 'type' => 'text', 'required' => true],
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
        ], 'brands', 'Brands');
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->index();
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
        return $this->index();
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
