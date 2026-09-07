<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latestCars = Car::query()->activeInventory()->with('coverImage')->latest()->limit(3)->get();
        $searchMakes = Car::query()->activeInventory()->distinct()->orderBy('make')->pluck('make');
        $searchModels = Car::query()->activeInventory()->get(['make', 'model'])->unique(fn (Car $car): string => $car->make.'|'.$car->model)->sortBy('model')->values();
        $business = (array) config('automercy.business');
        $locations = (array) config('automercy.locations');
        $whatsappUrl = $business['whatsapp_url'].'?text='.rawurlencode('Hello Auto Mercy, I would like help finding a car.');
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'AutoDealer',
            'name' => $business['name'],
            'url' => route('home'),
            'logo' => asset('images/auto-mercy-logo.webp'),
            'email' => $business['email'],
            'telephone' => $business['phone_e164'],
        ];

        return view('home', compact('latestCars', 'searchMakes', 'searchModels', 'business', 'locations', 'whatsappUrl', 'structuredData'));
    }
}
