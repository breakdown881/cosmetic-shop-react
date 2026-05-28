<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductCommentController extends Controller
{
    public function all()
    {
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'comments',
                'apiUrl' => route('admin.api.comments.index'),
                'title' => __('translate.comments'),
                'canCreate' => false,
                'canEdit' => true,
                'canDelete' => false,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.comments')],
                ],
                'columns' => $this->columns(),
                'fields' => $this->fields(),
                'labels' => $this->labels(),
        ], 'comments', 'Product comments');
    }

    public function index(Product $product)
    {
        return \App\Support\AdminReactShell::render('AdminApiResourceManager', [
                'resourceName' => 'product-comments',
                'apiUrl' => route('admin.api.products.comments.index', ['product' => $product->id]),
                'title' => 'Comments: ' . $product->name,
                'canCreate' => false,
                'canEdit' => true,
                'canDelete' => false,
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['href' => route('admin.product.index'), 'label' => __('translate.products')],
                    ['active' => true, 'label' => __('translate.comments')],
                ],
                'columns' => $this->columns(),
                'fields' => $this->fields(),
                'labels' => $this->labels(),
        ], 'comments', 'Product comments');
    }

    private function columns(): array
    {
        return [
            ['key' => 'product_name', 'label' => __('translate.products')],
            ['key' => 'fullname', 'label' => 'Customer'],
            ['key' => 'email', 'label' => __('translate.email')],
            ['key' => 'star', 'label' => 'Star'],
            ['key' => 'description', 'label' => 'Comment'],
            ['key' => 'active', 'label' => __('translate.status'), 'type' => 'boolean'],
            ['key' => 'created_at', 'label' => __('translate.createdAt')],
        ];
    }

    private function fields(): array
    {
        return [
            [
                'name' => 'active',
                'label' => __('translate.status'),
                'type' => 'select',
                'required' => true,
                'options' => [
                    ['value' => 1, 'label' => __('translate.active')],
                    ['value' => 0, 'label' => __('translate.inactive')],
                ],
            ],
        ];
    }

    private function labels(): array
    {
        return [
            'edit' => __('translate.edit'),
            'save' => __('translate.save'),
            'cancel' => __('translate.cancel'),
            'management' => __('translate.management'),
            'empty' => __('translate.noData') === 'translate.noData' ? 'Không có dữ liệu.' : __('translate.noData'),
        ];
    }
}
