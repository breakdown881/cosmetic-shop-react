<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(private readonly NewsletterService $newsletters) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->newsletters->subscribers(),
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $this->newsletters->send($data);

        return response()->json(['message' => 'Newsletter sent.']);
    }
}
