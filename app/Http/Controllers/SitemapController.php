<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Queries\CarInventoryQuery;
use App\Support\SeoUrl;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(CarInventoryQuery $inventoryQuery, SeoUrl $urls): Response
    {
        $contentLastModified = CarbonImmutable::parse((string) config('automercy.seo.content_last_modified'));
        $entries = collect(['home', 'services', 'contact'])
            ->map(fn (string $route): array => [
                'url' => $urls->route($route),
                'lastmod' => $contentLastModified->toAtomString(),
            ]);

        if ($inventoryQuery->hasPublicInventory()) {
            $inventoryUpdatedAt = Car::query()->activeInventory()->max('updated_at');
            $entries->push([
                'url' => $urls->route('cars.index'),
                'lastmod' => $inventoryUpdatedAt
                    ? CarbonImmutable::parse($inventoryUpdatedAt)->toAtomString()
                    : $contentLastModified->toAtomString(),
            ]);
        }

        Car::query()
            ->whereIn('status', [CarStatus::Available, CarStatus::Reserved, CarStatus::Sold])
            ->orderBy('id')
            ->get()
            ->each(fn (Car $car) => $entries->push([
                'url' => $urls->route('cars.show', $car),
                'lastmod' => $car->updated_at?->toAtomString(),
            ]));

        foreach ((array) config('automercy.seo.landings') as $key => $landing) {
            if (! ($landing['published'] ?? false)) {
                continue;
            }

            $matchingCars = Car::query()->activeInventory();
            foreach ((array) $landing['filter'] as $field => $value) {
                $matchingCars->where($field, $value);
            }

            if (! $matchingCars->exists()) {
                continue;
            }

            [$type, $slug] = explode('/', $key, 2);
            $inventoryUpdatedAt = $matchingCars->max('updated_at');
            $entries->push([
                'url' => $urls->route('cars.landings.show', compact('type', 'slug')),
                'lastmod' => collect([
                    CarbonImmutable::parse((string) $landing['updated_at']),
                    $inventoryUpdatedAt ? CarbonImmutable::parse($inventoryUpdatedAt) : null,
                ])->filter()->sort()->last()?->toAtomString(),
            ]);
        }

        return response()
            ->view('sitemap', ['entries' => $entries->unique('url')->values()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
