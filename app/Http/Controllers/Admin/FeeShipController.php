<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FeeShipController extends Controller
{
    public function index()
    {
        $canWrite = in_array(Auth::guard('admin')->user()?->role, ['MANAGER', 'ADMIN'], true);

        return view('admin.api-resource', [
            'title' => 'Fee Ships',
            'currentMenu' => 'feeships',
            'componentProps' => [
                'resourceName' => 'feeships',
                'apiUrl' => route('admin.api.feeships.index'),
                'title' => __('translate.feeShips'),
                'canCreate' => $canWrite,
                'canEdit' => $canWrite,
                'canDelete' => $canWrite,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.feeShips')],
                ],
                'columns' => [
                    ['key' => 'province_name', 'label' => 'Province'],
                    ['key' => 'province_type', 'label' => 'Province type'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'created_at', 'label' => __('translate.createdAt')],
                    ['key' => 'updated_at', 'label' => __('translate.updatedAt')],
                ],
                'fields' => [
                    ['name' => 'province_name', 'label' => 'Province', 'type' => 'text', 'required' => true],
                    ['name' => 'province_type', 'label' => 'Province type', 'type' => 'text', 'required' => true],
                    ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'required' => true],
                ],
                'labels' => $this->labels(),
            ],
        ]);
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
}
