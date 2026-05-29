<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LiveChatMessageRequest;
use App\Services\Customer\CustomerLiveChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveChatController extends Controller
{
    public function __construct(private readonly CustomerLiveChatService $liveChat) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->liveChat->conversation($request->session(), Auth::guard('web')->user()),
        ]);
    }

    public function store(LiveChatMessageRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->liveChat->send($request->session(), Auth::guard('web')->user(), $request->validated('message')),
        ], 201);
    }
}
