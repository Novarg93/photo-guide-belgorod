<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactInquiryRequest;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ContactUs', [
            'metaTitle' => 'Contact Us',
            'metaDescription' => 'Send a request and we will get back to you as soon as possible.',
        ]);
    }

    public function store(StoreContactInquiryRequest $request): RedirectResponse
    {
        ContactInquiry::query()->create($request->validated());

        return to_route('contact');
    }
}
