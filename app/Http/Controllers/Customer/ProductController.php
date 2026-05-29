<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerProductListRequest;
use App\Services\Customer\CustomerProductService;
use App\Support\PublicReactShell;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly CustomerProductService $products,
        private readonly PublicReactShell $shell,
    ) {}

    public function index(CustomerProductListRequest $request): Response
    {
        return $this->shell->render(
            'CustomerProductIndex',
            $this->products->indexProps($request->filters()),
            'Sản phẩm'
        );
    }

    public function category(CustomerProductListRequest $request, string $category): Response
    {
        $props = $this->products->categoryProps($category, $request->filters());

        return $this->shell->render('CustomerProductIndex', $props, $props['title']);
    }

    public function brand(CustomerProductListRequest $request, string $brand): Response
    {
        $props = $this->products->brandProps($brand, $request->filters());

        return $this->shell->render('CustomerProductIndex', $props, $props['title']);
    }
}
