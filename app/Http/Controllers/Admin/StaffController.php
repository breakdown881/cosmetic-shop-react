<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'staffs',
                'apiUrl' => route('admin.api.staffs.index'),
                'title' => __('translate.staffs'),
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.staffs')],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => __('translate.name')],
                    ['key' => 'email', 'label' => __('translate.email')],
                    ['key' => 'role', 'label' => 'Role'],
                    ['key' => 'is_active', 'label' => __('translate.status'), 'type' => 'boolean'],
                    ['key' => 'created_at', 'label' => __('translate.createdAt')],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => __('translate.name'), 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => __('translate.email'), 'type' => 'email', 'required' => true],
                    ['name' => 'password', 'label' => __('translate.password'), 'type' => 'password', 'createOnlyRequired' => true],
                    ['name' => 'password_confirmation', 'label' => 'Confirm password', 'type' => 'password', 'createOnlyRequired' => true],
                    ['name' => 'phone_number', 'label' => 'Phone', 'type' => 'text'],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
                    [
                        'name' => 'role',
                        'label' => 'Role',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'MANAGER', 'label' => 'MANAGER'],
                            ['value' => 'ADMIN', 'label' => 'ADMIN'],
                            ['value' => 'STAFF', 'label' => 'STAFF'],
                        ],
                    ],
                    [
                        'name' => 'is_active',
                        'label' => __('translate.status'),
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 1, 'label' => __('translate.active')],
                            ['value' => 0, 'label' => __('translate.inactive')],
                        ],
                    ],
                ],
                'labels' => $this->labels(),
        ], 'staffs', 'Staffs');
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
