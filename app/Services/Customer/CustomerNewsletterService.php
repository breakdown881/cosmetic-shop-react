<?php

namespace App\Services\Customer;

use App\Models\NewsLetter;
use App\Repositories\Customer\CustomerNewsletterRepository;

class CustomerNewsletterService
{
    public function __construct(private readonly CustomerNewsletterRepository $newsletters) {}

    public function subscribe(string $email): array
    {
        $subscriber = $this->newsletters->subscribe(strtolower($email));

        return $this->format($subscriber);
    }

    public function exists(string $email): bool
    {
        return $this->newsletters->findByEmail(strtolower($email)) !== null;
    }

    private function format(NewsLetter $subscriber): array
    {
        return [
            'id' => $subscriber->id,
            'email' => $subscriber->email,
            'created' => $subscriber->wasRecentlyCreated,
            'created_at' => optional($subscriber->created_at)->toDateTimeString(),
        ];
    }
}
