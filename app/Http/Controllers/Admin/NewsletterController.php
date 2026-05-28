<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsLetter;

class NewsletterController extends Controller
{
    public function index()
    {
        return \App\Support\AdminReactShell::render('AdminNewsletterManager', [
                'apiUrl' => route('admin.api.newsletters.index'),
                'sendUrl' => route('admin.api.newsletters.send'),
                'labels' => [
                    'body' => 'Nội dung',
                    'createdAt' => 'Ngày đăng ký',
                    'email' => 'Email',
                    'empty' => 'Chưa có email đăng ký.',
                    'send' => 'Gửi mail',
                    'subject' => 'Tiêu đề',
                    'title' => 'Newsletter',
                ],
        ], 'newsletters', 'Newsletter');
    }
}
