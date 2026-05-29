<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerNavigationService;
use App\Support\PublicReactShell;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function __construct(
        private readonly CustomerNavigationService $navigation,
        private readonly PublicReactShell $shell,
    ) {}

    public function account(): Response
    {
        return $this->placeholder('Tài khoản', 'Customer account management will be implemented in the account phase.');
    }

    private function placeholder(string $title, string $description): Response
    {
        return $this->shell->render('CustomerPlaceholderPage', [
            'title' => $title,
            'description' => $description,
            'navItems' => $this->navigation->navItems(),
        ], $title);
    }
}
