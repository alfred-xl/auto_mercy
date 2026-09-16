<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Support\SeoStructuredData;
use App\Support\SeoUrl;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(SeoUrl $urls, SeoStructuredData $structuredData): View
    {
        $latestCars = Car::query()->activeInventory()->with('coverImage')->latest()->limit(3)->get();
        $searchMakes = Car::query()->activeInventory()->distinct()->orderBy('make')->pluck('make');
        $searchModels = Car::query()->activeInventory()->get(['make', 'model'])->unique(fn (Car $car): string => $car->make.'|'.$car->model)->sortBy('model')->values();
        $business = (array) config('automercy.business');
        $locations = (array) config('automercy.locations');
        $whatsappUrl = $business['whatsapp_url'].'?text='.rawurlencode('Hello Auto Mercy, I would like help finding a car.');
        $canonical = $urls->route('home');
        $structuredData = $structuredData->businessGraph($canonical);

        return view('home', compact('latestCars', 'searchMakes', 'searchModels', 'business', 'locations', 'whatsappUrl', 'structuredData', 'canonical'));
    }
}
