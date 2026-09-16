<?php

namespace App\Support;

use App\Enums\CarStatus;
use App\Enums\ListingCategory;
use App\Models\Car;

class SeoStructuredData
{
    public function __construct(private readonly SeoUrl $urls) {}

    /** @return array<string, mixed> */
    public function businessGraph(string $pageUrl, bool $includeLocations = true): array
    {
        $business = (array) config('automercy.business');
        $organizationId = $this->organizationId();
        $graph = [[
            '@type' => 'Organization',
            '@id' => $organizationId,
            'name' => $business['name'],
            'legalName' => $business['legal_name'],
            'url' => $this->urls->route('home'),
            'email' => $business['email'],
            'telephone' => $business['phone_e164'],
        ]];

        if ($includeLocations) {
            foreach ((array) config('automercy.locations') as $location) {
                $graph[] = [
                    '@type' => 'AutoDealer',
                    '@id' => $this->urls->route('home').'#location-'.$location['slug'],
                    'name' => $business['name'].' - '.$location['name'],
                    'url' => $pageUrl.'#locations',
                    'parentOrganization' => ['@id' => $organizationId],
                    'telephone' => $business['phone_e164'],
                    'email' => $business['email'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $location['street_address'],
                        'addressLocality' => $location['address_locality'],
                        'addressRegion' => $location['address_region'],
                        'addressCountry' => 'NG',
                    ],
                    'openingHoursSpecification' => [[
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                        'opens' => '08:00',
                        'closes' => '18:00',
                    ]],
                    'hasMap' => $location['map_url'],
                ];
            }
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    /** @param list<array{name: string, url: string}> $breadcrumbs */
    public function vehicle(Car $car, array $images, array $breadcrumbs): array
    {
        $business = (array) config('automercy.business');
        $canonical = $this->urls->route('cars.show', $car);
        $vehicle = [
            '@type' => ['Product', 'Car'],
            '@id' => $canonical.'#vehicle',
            'name' => $car->display_name,
            'sku' => $car->stock_number,
            'url' => $canonical,
            'description' => $this->plainText($car->description),
            'brand' => ['@type' => 'Brand', 'name' => $car->make],
            'model' => $car->model,
            'vehicleModelDate' => (string) $car->year,
            'offers' => [
                '@type' => 'Offer',
                'url' => $canonical,
                'price' => (int) $car->price_amount,
                'priceCurrency' => (string) config('automercy.currency'),
                'availability' => $car->status === CarStatus::Available
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => ['@id' => $this->organizationId()],
            ],
        ];

        if ($images !== []) {
            $vehicle['image'] = $images;
        }

        if ($car->listing_category === ListingCategory::BrandNew) {
            $vehicle['itemCondition'] = 'https://schema.org/NewCondition';
        } elseif ($car->listing_category === ListingCategory::ForeignUsed) {
            $vehicle['itemCondition'] = 'https://schema.org/UsedCondition';
        }

        if ($car->mileage !== null && $car->mileage_unit !== null) {
            $vehicle['mileageFromOdometer'] = [
                '@type' => 'QuantitativeValue',
                'value' => (int) $car->mileage,
                'unitCode' => $car->mileage_unit->value === 'mi' ? 'SMI' : 'KMT',
            ];
        }

        foreach ([
            'fuelType' => $car->fuel_type?->label(),
            'vehicleTransmission' => $car->transmission?->label(),
            'color' => $car->exterior_colour,
            'vehicleInteriorColor' => $car->interior_colour,
            'bodyType' => $car->body_type,
        ] as $property => $value) {
            if (filled($value)) {
                $vehicle[$property] = $value;
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $vehicle,
                [
                    '@type' => 'Organization',
                    '@id' => $this->organizationId(),
                    'name' => $business['name'],
                    'legalName' => $business['legal_name'],
                    'url' => $this->urls->route('home'),
                    'email' => $business['email'],
                    'telephone' => $business['phone_e164'],
                ],
                $this->breadcrumbList($breadcrumbs),
            ],
        ];
    }

    /** @param list<array{name: string, url: string}> $breadcrumbs */
    public function breadcrumbs(array $breadcrumbs): array
    {
        return ['@context' => 'https://schema.org', '@graph' => [$this->breadcrumbList($breadcrumbs)]];
    }

    public function organizationId(): string
    {
        return $this->urls->route('home').'#organization';
    }

    public function plainText(?string $value): string
    {
        return str(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'))->squish()->toString();
    }

    /** @param list<array{name: string, url: string}> $breadcrumbs */
    private function breadcrumbList(array $breadcrumbs): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbs)->values()->map(fn (array $breadcrumb, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'],
            ])->all(),
        ];
    }
}
