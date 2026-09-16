<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class PageController extends Controller
{
    public function services(): View
    {
        return view('pages.services');
    }

    public function installment(): View
    {
        return view('pages.installment');
    }

    public function tradeIn(): View
    {
        $products = Product::active()->orderBy('name')->get(['id', 'name', 'base_price', 'series_id']);

        return view('pages.trade-in', compact('products'));
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
