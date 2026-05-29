<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ChatbotMessageRequest;
use App\Services\Customer\CustomerChatbotService;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(private readonly CustomerChatbotService $chatbot) {}

    public function store(ChatbotMessageRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->chatbot->answer($request->user(), $request->validated('message')),
        ]);
    }
}
