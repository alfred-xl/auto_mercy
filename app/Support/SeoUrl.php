<?php

namespace App\Support;

use InvalidArgumentException;

class SeoUrl
{
    public function baseUrl(): string
    {
        $baseUrl = rtrim((string) config('automercy.seo.base_url'), '/');

        if (! in_array(parse_url($baseUrl, PHP_URL_SCHEME), ['http', 'https'], true) || blank(parse_url($baseUrl, PHP_URL_HOST))) {
            throw new InvalidArgumentException('AUTOMERCY SEO base URL must be an absolute HTTP or HTTPS URL.');
        }

        return $baseUrl;
    }

    public function route(string $name, mixed $parameters = [], array $query = []): string
    {
        $path = route($name, $parameters, false);

        return $this->withQuery($this->absolute($path), $query);
    }

    public function absolute(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $applicationHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $pathHost = parse_url($path, PHP_URL_HOST);

            if ($applicationHost && $pathHost === $applicationHost) {
                $relativePath = (string) parse_url($path, PHP_URL_PATH);
                $query = parse_url($path, PHP_URL_QUERY);

                return $this->baseUrl().'/'.ltrim($relativePath, '/').($query ? '?'.$query : '');
            }

            return $path;
        }

        return $this->baseUrl().'/'.ltrim($path, '/');
    }

    public function withQuery(string $url, array $query): string
    {
        $query = collect($query)
            ->reject(fn (mixed $value): bool => $value === null || $value === '')
            ->all();

        return $query === [] ? $url : $url.'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}
