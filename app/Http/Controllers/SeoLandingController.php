<?php

namespace App\Http\Controllers;

use App\Queries\CarInventoryQuery;
use App\Support\AttributionParameters;
use App\Support\SeoStructuredData;
use App\Support\SeoUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SeoLandingController extends Controller
{
    public function __invoke(
        Request $request,
        string $type,
        string $slug,
        CarInventoryQuery $inventoryQuery,
        AttributionParameters $attribution,
        SeoUrl $urls,
        SeoStructuredData $structuredData,
    ): View|RedirectResponse {
        $key = $type.'/'.$slug;
        $landing = config('automercy.seo.landings.'.$key);

        abort_unless(is_array($landing) && ($landing['published'] ?? false), 404);

        $pageValue = $request->query('page');

        if ($pageValue !== null && (! is_scalar($pageValue) || ! ctype_digit((string) $pageValue))) {
            abort(404);
        }

        $page = (int) ($pageValue ?? 1);
        abort_if($page < 1, 404);
        $hasNonCanonicalPage = $pageValue !== null && (string) $pageValue !== (string) $page;

        if (($page === 1 && $pageValue !== null) || $hasNonCanonicalPage || $attribution->hasUnsupported($request->query(), ['page'])) {
            return redirect()->to(route('cars.landings.show', [
                'type' => $type,
                'slug' => $slug,
                ...($page > 1 ? ['page' => $page] : []),
                ...$attribution->from($request->query()),
            ], false), 301);
        }

        $filters = [...(array) $landing['filter'], ...($page > 1 ? ['page' => $page] : [])];
        $cars = $inventoryQuery->paginate($filters);
        abort_if($page > 1 && $cars->isEmpty(), 404);

        $canonical = $urls->route('cars.landings.show', compact('type', 'slug'), $page > 1 ? ['page' => $page] : []);
        $breadcrumbs = [
            ['name' => 'Home', 'url' => $urls->route('home')],
            ['name' => 'Cars', 'url' => $urls->route('cars.index')],
            ['name' => $landing['heading'], 'url' => $canonical],
        ];

        return view('cars.landing', [
            'landing' => $landing,
            'cars' => $cars,
            'canonical' => $canonical,
            'robots' => $cars->isEmpty() ? 'noindex,follow' : 'index,follow',
            'breadcrumbs' => $breadcrumbs,
            'structuredData' => $structuredData->breadcrumbs($breadcrumbs),
            'relatedLandings' => $this->relatedLandings($key, $inventoryQuery, $urls),
        ]);
    }

    /** @return list<array{name: string, url: string}> */
    private function relatedLandings(string $currentKey, CarInventoryQuery $inventoryQuery, SeoUrl $urls): array
    {
        return collect((array) config('automercy.seo.landings'))
            ->reject(fn (array $landing, string $key): bool => $key === $currentKey || ! ($landing['published'] ?? false))
            ->filter(fn (array $landing): bool => $inventoryQuery->paginate((array) $landing['filter'])->isNotEmpty())
            ->map(function (array $landing, string $key) use ($urls): array {
                [$type, $slug] = explode('/', $key, 2);

                return ['name' => $landing['heading'], 'url' => $urls->route('cars.landings.show', compact('type', 'slug'))];
            })
            ->values()
            ->all();
    }
}
