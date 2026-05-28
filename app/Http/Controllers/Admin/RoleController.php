<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.api-resource', [
            'title' => 'Roles',
            'currentMenu' => 'roles',
            'componentProps' => [
                'resourceName' => 'roles',
                'apiUrl' => route('admin.api.roles.index'),
                'title' => __('translate.listRole'),
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.listRole')],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => __('translate.name')],
                    ['key' => 'created_at', 'label' => __('translate.createdAt')],
                    ['key' => 'updated_at', 'label' => __('translate.updatedAt')],
                ],
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => __('translate.name'),
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'MANAGER', 'label' => 'MANAGER'],
                            ['value' => 'ADMIN', 'label' => 'ADMIN'],
                            ['value' => 'STAFF', 'label' => 'STAFF'],
                        ],
                    ],
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
            'empty' => __('translate.noData') === 'translate.noData' ? 'Không có d? li?u.' : __('translate.noData'),
            'deleteConfirm' => __('translate.deleteConfirm') === 'translate.deleteConfirm' ? 'B?n có ch?c mu?n xóa?' : __('translate.deleteConfirm'),
        ];
    }
}
