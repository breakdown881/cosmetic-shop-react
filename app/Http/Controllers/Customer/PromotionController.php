<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\VoucherValidationRequest;
use App\Services\Customer\CustomerPromotionService;
use App\Support\PublicReactShell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PromotionController extends Controller
{
    public function __construct(
        private readonly CustomerPromotionService $promotions,
        private readonly PublicReactShell $shell,
    ) {}

    public function index(): Response
    {
        $props = $this->promotions->props();

        return $this->shell->render('CustomerPromotionPage', $props, $props['title']);
    }

    public function validateVoucher(VoucherValidationRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->promotions->validateVoucher(
                $request->session(),
                $request->string('discount_code')->toString()
            ),
        ]);
    }
}
