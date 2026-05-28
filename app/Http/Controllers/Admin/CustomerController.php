<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function index()
    {
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'customers',
                'apiUrl' => route('admin.api.customers.index'),
                'title' => __('translate.customers'),
                'canCreate' => false,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.customers')],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => __('translate.name')],
                    ['key' => 'email', 'label' => __('translate.email')],
                    ['key' => 'email_verified_at', 'label' => 'Email verified at'],
                    ['key' => 'created_at', 'label' => __('translate.createdAt')],
                    ['key' => 'updated_at', 'label' => __('translate.updatedAt')],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => __('translate.name'), 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => __('translate.email'), 'type' => 'email', 'required' => true],
                ],
                'labels' => $this->labels(),
        ], 'customers', 'Customers');
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
