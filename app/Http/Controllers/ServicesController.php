<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ServicesController extends Controller
{
    public function __invoke(): View
    {
        $business = (array) config('automercy.business');
        $whatsappUrl = $business['whatsapp_url'].'?text='.rawurlencode('Hello Auto Mercy, I would like to know more about your vehicle services.');

        return view('services', compact('business', 'whatsappUrl'));
    }
}
