<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    public function index()
    {
        $canWrite = in_array(Auth::guard('admin')->user()?->role, ['MANAGER', 'ADMIN'], true);

        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'discounts',
                'apiUrl' => route('admin.api.discounts.index'),
                'title' => __('translate.discounts'),
                'canCreate' => $canWrite,
                'canEdit' => $canWrite,
                'canDelete' => $canWrite,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.discounts')],
                ],
                'columns' => [
                    ['key' => 'code', 'label' => __('translate.code')],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'is_fixed', 'label' => 'Fixed', 'type' => 'boolean'],
                    ['key' => 'discount_amount', 'label' => 'Amount'],
                    ['key' => 'starts_at', 'label' => 'Starts at'],
                    ['key' => 'expires_at', 'label' => 'Expires at'],
                ],
                'fields' => [
                    ['name' => 'code', 'label' => __('translate.code'), 'type' => 'text', 'required' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'text', 'required' => true],
                    [
                        'name' => 'is_fixed',
                        'label' => 'Fixed',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 1, 'label' => 'Fixed amount'],
                            ['value' => 0, 'label' => 'Percent'],
                        ],
                    ],
                    ['name' => 'discount_amount', 'label' => 'Amount', 'type' => 'number', 'required' => true],
                    ['name' => 'starts_at', 'label' => 'Starts at', 'type' => 'text'],
                    ['name' => 'expires_at', 'label' => 'Expires at', 'type' => 'text'],
                ],
                'labels' => $this->labels(),
        ], 'discounts', 'Discounts');
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
