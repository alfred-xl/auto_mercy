<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarStand;
use App\Models\Make;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latestCars = Car::query()
            ->available()
            ->with(['make', 'carModel', 'bodyType', 'carStand', 'primaryImage'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $availableMakes = Make::query()
            ->active()
            ->whereHas('cars', fn (Builder $query) => $query->available())
            ->withCount(['cars as available_cars_count' => fn (Builder $query) => $query->available()])
            ->get();

        $searchMakes = Make::query()->active()->get();
        $searchModels = CarModel::query()->active()->with('make:id,name')->get();
        $searchStands = CarStand::query()->active()->get();
        $business = (array) config('automercy.business');
        $locations = (array) config('automercy.locations');
        $whatsappUrl = $business['whatsapp_url'].'?text='.rawurlencode('Hello Auto Mercy, I would like help finding a car.');

        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => route('home').'#organization',
                    'name' => $business['name'],
                    'legalName' => $business['legal_name'],
                    'url' => route('home'),
                    'logo' => asset('images/auto-mercy-logo.webp'),
                    'email' => $business['email'],
                    'telephone' => $business['phone_e164'],
                    'foundingDate' => (string) $business['operating_since'],
                ],
                ...collect($locations)->map(fn (array $location): array => [
                    '@type' => 'AutoDealer',
                    '@id' => route('home').'#'.$location['slug'],
                    'name' => $business['name'].' — '.$location['name'],
                    'parentOrganization' => ['@id' => route('home').'#organization'],
                    'telephone' => $business['phone_e164'],
                    'email' => $business['email'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $location['address'],
                        'addressLocality' => 'Lagos',
                        'addressRegion' => 'Lagos',
                        'addressCountry' => 'NG',
                    ],
                    'openingHours' => 'Mo-Sa 08:00-18:00',
                ])->all(),
            ],
        ];

        return view('home', compact(
            'latestCars',
            'availableMakes',
            'searchMakes',
            'searchModels',
            'searchStands',
            'business',
            'locations',
            'whatsappUrl',
            'structuredData',
        ));
    }
}
