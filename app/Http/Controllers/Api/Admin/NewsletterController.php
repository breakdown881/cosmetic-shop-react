<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMessage;
use App\Models\NewsLetter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => NewsLetter::query()
                ->latest()
                ->get(['id', 'email', 'created_at'])
                ->map(fn (NewsLetter $subscriber) => [
                    'id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'created_at' => optional($subscriber->created_at)->toDateTimeString(),
                ]),
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        NewsLetter::query()
            ->pluck('email')
            ->unique()
            ->each(fn (string $email) => Mail::to($email)->send(
                new NewsletterMessage($data['subject'], $data['body'])
            ));

        return response()->json(['message' => 'Newsletter sent.']);
    }
}
