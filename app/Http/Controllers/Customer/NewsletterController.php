<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\NewsletterSubscribeRequest;
use App\Services\Customer\CustomerNewsletterService;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function __construct(private readonly CustomerNewsletterService $newsletters) {}

    public function store(NewsletterSubscribeRequest $request): JsonResponse
    {
        $subscription = $this->newsletters->subscribe($request->string('email')->toString());

        return response()->json([
            'data' => $subscription,
        ], $subscription['created'] ? 201 : 200);
    }
}
