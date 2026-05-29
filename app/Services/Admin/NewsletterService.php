<?php

namespace App\Services\Admin;

use App\Mail\NewsletterMessage;
use App\Models\NewsLetter;
use App\Repositories\Admin\NewsletterRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NewsletterService
{
    public function __construct(private readonly NewsletterRepository $newsletters) {}

    public function subscribers(): Collection
    {
        return $this->newsletters->allSubscribers()
            ->map(fn (NewsLetter $subscriber) => [
                'id' => $subscriber->id,
                'email' => $subscriber->email,
                'created_at' => optional($subscriber->created_at)->toDateTimeString(),
            ]);
    }

    public function send(array $data): void
    {
        $this->newsletters->uniqueEmails()
            ->each(fn (string $email) => Mail::to($email)->send(
                new NewsletterMessage($data['subject'], $data['body'])
            ));
    }
}
