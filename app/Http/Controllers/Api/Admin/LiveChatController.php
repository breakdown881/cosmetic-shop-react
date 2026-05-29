<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LiveChatReplyRequest;
use App\Services\Admin\LiveChatService;
use Illuminate\Http\JsonResponse;

class LiveChatController extends Controller
{
    public function __construct(private readonly LiveChatService $liveChat) {}

    public function index(): JsonResponse
    {
        return response()->json($this->liveChat->inbox());
    }

    public function show(int|string $conversation): JsonResponse
    {
        return response()->json([
            'data' => $this->liveChat->find($conversation),
        ]);
    }

    public function store(LiveChatReplyRequest $request, int|string $conversation): JsonResponse
    {
        return response()->json([
            'data' => $this->liveChat->reply($conversation, (int) $request->user('admin')->id, $request->validated('message')),
        ], 201);
    }
}
