@php
    $galleryImages = $record->images
        ->map(fn ($image): array => [
            'url' => $image->variantUrl('large'),
            'thumbnail' => $image->variantUrl('thumbnail'),
            'alt' => $image->alt_text ?: $record->display_name,
        ])
        ->values();
@endphp

@if ($galleryImages->isEmpty())
    <div class="am-gallery-empty">
        <x-filament::icon icon="heroicon-o-photo" class="am-gallery-empty-icon" />
        <div>
            <p class="am-gallery-empty-title">No gallery images</p>
            <p class="am-gallery-empty-copy">Add vehicle photos from the edit page.</p>
        </div>
    </div>
@else
    <div
        class="am-gallery"
        x-data="{ images: @js($galleryImages), active: 0, lightbox: false }"
        x-on:keydown.escape.window="lightbox = false"
        x-on:keydown.right.window="if (lightbox) active = active === images.length - 1 ? 0 : active + 1"
        x-on:keydown.left.window="if (lightbox) active = active === 0 ? images.length - 1 : active - 1"
    >
        <button type="button" class="am-gallery-main" x-on:click="lightbox = true" aria-label="Open image in full-screen preview">
            <img x-bind:src="images[active].url" x-bind:alt="images[active].alt">
            <span class="am-gallery-primary" x-show="active === 0">Primary image</span>
            <span class="am-gallery-expand">
                <x-filament::icon icon="heroicon-o-arrows-pointing-out" />
                <span>Full screen</span>
            </span>
        </button>

        <div class="am-gallery-thumbnails" aria-label="Vehicle gallery thumbnails">
            @foreach ($galleryImages as $index => $image)
                <button
                    type="button"
                    class="am-gallery-thumbnail"
                    x-on:click="active = {{ $index }}"
                    x-bind:aria-current="active === {{ $index }} ? 'true' : 'false'"
                    x-bind:style="active === {{ $index }} ? 'border-color: var(--primary-600); box-shadow: 0 0 0 2px var(--primary-200)' : ''"
                    aria-label="Show image {{ $index + 1 }}"
                >
                    <img src="{{ $image['thumbnail'] }}" alt="{{ $image['alt'] }}">
                </button>
            @endforeach
        </div>

        <template x-teleport="body">
            <div
                x-show="lightbox"
                x-transition.opacity
                x-cloak
                class="am-gallery-lightbox"
                role="dialog"
                aria-modal="true"
                aria-label="Vehicle image preview"
                x-trap.noreturn.noautofocus="lightbox"
                x-on:click.self="lightbox = false"
            >
                <button type="button" class="am-gallery-lightbox-close" x-on:click="lightbox = false" aria-label="Close full-screen preview">
                    <x-filament::icon icon="heroicon-o-x-mark" />
                </button>
                <button
                    type="button"
                    class="am-gallery-lightbox-previous"
                    x-show="images.length > 1"
                    x-on:click="active = active === 0 ? images.length - 1 : active - 1"
                    aria-label="Previous image"
                >
                    <x-filament::icon icon="heroicon-o-chevron-left" />
                </button>
                <img class="am-gallery-lightbox-image" x-bind:src="images[active].url" x-bind:alt="images[active].alt">
                <button
                    type="button"
                    class="am-gallery-lightbox-next"
                    x-show="images.length > 1"
                    x-on:click="active = active === images.length - 1 ? 0 : active + 1"
                    aria-label="Next image"
                >
                    <x-filament::icon icon="heroicon-o-chevron-right" />
                </button>
                <span class="am-gallery-lightbox-count" x-text="(active + 1) + ' / ' + images.length"></span>
            </div>
        </template>
    </div>
@endif

<style>
    .am-gallery-empty {
        display: flex;
        min-height: 16rem;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        border: 1px dashed #d1d5db;
        border-radius: .75rem;
        color: #6b7280;
        text-align: left;
    }

    .am-gallery-empty-icon { width: 2rem; height: 2rem; }
    .am-gallery-empty-title { font-weight: 600; color: #111827; }
    .am-gallery-empty-copy { margin-top: .25rem; font-size: .875rem; }
    .am-gallery { display: grid; gap: .875rem; }

    .am-gallery-main {
        position: relative;
        display: block;
        width: 100%;
        overflow: hidden;
        aspect-ratio: 16 / 9;
        border-radius: .75rem;
        background: #111827;
        cursor: zoom-in;
    }

    .am-gallery-main > img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .am-gallery-primary, .am-gallery-expand {
        position: absolute;
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        border-radius: .5rem;
        background: rgb(17 24 39 / 85%);
        color: white;
        font-size: .75rem;
        font-weight: 600;
    }

    .am-gallery-primary { top: .75rem; left: .75rem; padding: .375rem .625rem; }
    .am-gallery-expand { right: .75rem; bottom: .75rem; padding: .5rem .625rem; }
    .am-gallery-expand svg { width: 1rem; height: 1rem; }

    .am-gallery-thumbnails {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(5.5rem, 7rem);
        gap: .625rem;
        overflow-x: auto;
        padding: .2rem;
        scrollbar-width: thin;
    }

    .am-gallery-thumbnail {
        overflow: hidden;
        width: 100%;
        aspect-ratio: 4 / 3;
        border: 2px solid transparent;
        border-radius: .625rem;
        background: #f3f4f6;
    }

    .am-gallery-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .am-gallery-lightbox {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 3rem 5rem;
        background: rgb(0 0 0 / 94%);
    }

    .am-gallery-lightbox-image {
        max-width: 100%;
        max-height: calc(100vh - 6rem);
        object-fit: contain;
    }

    .am-gallery-lightbox-close,
    .am-gallery-lightbox-previous,
    .am-gallery-lightbox-next {
        position: absolute;
        display: grid;
        width: 2.75rem;
        height: 2.75rem;
        place-items: center;
        border-radius: 999px;
        background: rgb(255 255 255 / 14%);
        color: white;
    }

    .am-gallery-lightbox-close { top: 1rem; right: 1rem; }
    .am-gallery-lightbox-previous { left: 1rem; top: calc(50% - 1.375rem); }
    .am-gallery-lightbox-next { right: 1rem; top: calc(50% - 1.375rem); }
    .am-gallery-lightbox-close svg,
    .am-gallery-lightbox-previous svg,
    .am-gallery-lightbox-next svg { width: 1.5rem; height: 1.5rem; }

    .am-gallery-lightbox-count {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: .8125rem;
    }

    @media (max-width: 640px) {
        .am-gallery-main { aspect-ratio: 4 / 3; }
        .am-gallery-expand span { display: none; }
        .am-gallery-lightbox { padding: 4rem 1rem; }
        .am-gallery-lightbox-image { max-height: calc(100vh - 8rem); }
    }
</style>
