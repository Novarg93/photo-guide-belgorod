<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AboutUsController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('AboutUs', [
            'metaTitle' => 'About Us',
            'metaDescription' => 'Learn why Photo Guide Belgorod was created, what problem it solves, and where the project is going.',
        ]);
    }
}
