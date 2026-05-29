<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateAccountRequest;
use App\Services\Customer\CustomerAccountService;
use App\Support\PublicReactShell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function __construct(
        private readonly CustomerAccountService $account,
        private readonly PublicReactShell $shell,
    ) {}

    public function show(Request $request): Response
    {
        $props = $this->account->props($request->user());

        return $this->shell->render('CustomerAccountPage', $props, $props['title']);
    }

    public function update(UpdateAccountRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->account->update($request->user(), $request->validated()),
        ]);
    }
}
