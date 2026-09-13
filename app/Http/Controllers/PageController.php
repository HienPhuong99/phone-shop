<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function services(): View
    {
        return view('pages.services');
    }

    public function policies(): View
    {
        return view('pages.policies.index');
    }

    public function warrantyPolicy(): View
    {
        return view('pages.policies.warranty');
    }

    public function returnsPolicy(): View
    {
        return view('pages.policies.returns');
    }

    public function shippingPolicy(): View
    {
        return view('pages.policies.shipping');
    }

    public function privacyPolicy(): View
    {
        return view('pages.policies.privacy');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }
}
