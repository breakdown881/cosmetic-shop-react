<?php

namespace App\Repositories\Customer;

use App\Models\NewsLetter;

class CustomerNewsletterRepository
{
    public function findByEmail(string $email): ?NewsLetter
    {
        return NewsLetter::query()
            ->where('email', $email)
            ->first();
    }

    public function subscribe(string $email): NewsLetter
    {
        return NewsLetter::query()->firstOrCreate(
            ['email' => $email],
            ['created_by' => null]
        );
    }
}
