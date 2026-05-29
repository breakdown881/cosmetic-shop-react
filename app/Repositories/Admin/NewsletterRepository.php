<?php

namespace App\Repositories\Admin;

use App\Models\NewsLetter;
use Illuminate\Support\Collection;

class NewsletterRepository
{
    public function allSubscribers(): Collection
    {
        return NewsLetter::query()
            ->latest()
            ->get(['id', 'email', 'created_at']);
    }

    public function uniqueEmails(): Collection
    {
        return NewsLetter::query()
            ->pluck('email')
            ->unique();
    }
}
