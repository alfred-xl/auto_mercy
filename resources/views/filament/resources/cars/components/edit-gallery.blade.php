@php
    $savedImages = $record?->exists
        ? $record->images->values()
        : collect();
    $coverImageId = $record?->coverImage?->getKey();
@endphp

<div class="am-edit-gallery">
    <div class="am-edit-gallery-heading">
        <div>
            <p class="am-edit-gallery-title">Saved photos</p>
            <p class="am-edit-gallery-copy">
                {{ $savedImages->count() }} {{ str('photo')->plural($savedImages->count()) }} currently saved.
                Select a photo to preview it.
            </p>
        </div>
    </div>

    @if ($savedImages->isEmpty())
        <div class="am-edit-gallery-empty">
            <x-filament::icon icon="heroicon-o-photo" class="am-edit-gallery-empty-icon" />
            <div>
                <p class="am-edit-gallery-empty-title">No saved photos yet</p>
                <p class="am-edit-gallery-empty-copy">Use the uploader below to add the first vehicle photos.</p>
            </div>
        </div>
    @else
        <div class="am-edit-gallery-grid">
            @foreach ($savedImages as $image)
                <article class="am-edit-gallery-card">
                    <a
                        href="{{ $image->variantUrl('large') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="am-edit-gallery-preview"
                        aria-label="Preview {{ basename($image->path) }}"
                    >
                        <img
                            src="{{ $image->variantUrl('thumbnail') }}"
                            alt="{{ $image->alt_text ?: $record->display_name }}"
                            loading="lazy"
                        >
                        <span class="am-edit-gallery-open" aria-hidden="true">
                            <x-filament::icon icon="heroicon-o-arrows-pointing-out" />
                        </span>
                    </a>

                    <div class="am-edit-gallery-remove">
                        {{ ($getAction('remove_saved_photo'))(['image' => $image->getKey()]) }}
                    </div>

                    <div class="am-edit-gallery-details">
                        <p class="am-edit-gallery-filename" title="{{ basename($image->path) }}">
                            {{ basename($image->path) }}
                        </p>
                        <div class="am-edit-gallery-badges">
                            @if ($coverImageId === $image->getKey())
                                <span class="am-edit-gallery-badge is-cover">Cover</span>
                            @endif
                            <span @class([
                                'am-edit-gallery-badge',
                                'is-ready' => $image->processing_status->value === 'ready',
                                'is-failed' => $image->processing_status->value === 'failed',
                            ])>
                                {{ $image->processing_status->label() }}
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="am-edit-gallery-help">
            Cover selection, ordering, replacement, and alternative text remain available in the Gallery manager below the form.
        </p>
    @endif
</div>

<style>
    .am-edit-gallery {
        display: grid;
        gap: 1rem;
    }

    .am-edit-gallery-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .am-edit-gallery-title,
    .am-edit-gallery-empty-title {
        color: #161513;
        font-size: .9375rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .am-edit-gallery-copy,
    .am-edit-gallery-empty-copy,
    .am-edit-gallery-help {
        color: #6b6b6b;
        font-size: .8125rem;
        line-height: 1.5;
    }

    .am-edit-gallery-copy {
        margin-top: .125rem;
    }

    .am-edit-gallery-empty {
        display: flex;
        min-height: 8rem;
        align-items: center;
        justify-content: center;
        gap: .75rem;
        border: 1px dashed #ded8d2;
        border-radius: .75rem;
        background: #faf8f6;
        padding: 1.25rem;
        text-align: left;
    }

    .am-edit-gallery-empty-icon {
        width: 1.75rem;
        height: 1.75rem;
        flex: none;
        color: #a1a1aa;
    }

    .am-edit-gallery-empty-copy {
        margin-top: .125rem;
    }

    .am-edit-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(9.5rem, 1fr));
        gap: .875rem;
    }

    .am-edit-gallery-card {
        position: relative;
        min-width: 0;
        overflow: hidden;
        border: 1px solid #ded8d2;
        border-radius: .75rem;
        background: #fff;
    }

    .am-edit-gallery-preview {
        position: relative;
        display: block;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        background: #faf8f6;
    }

    .am-edit-gallery-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 160ms ease;
    }

    .am-edit-gallery-preview:hover img {
        transform: scale(1.025);
    }

    .am-edit-gallery-open {
        position: absolute;
        right: .5rem;
        bottom: .5rem;
        display: grid;
        width: 1.75rem;
        height: 1.75rem;
        place-items: center;
        border-radius: .45rem;
        background: rgb(24 24 27 / 78%);
        color: #fff;
        opacity: 0;
        transition: opacity 160ms ease;
    }

    .am-edit-gallery-open svg {
        width: 1rem;
        height: 1rem;
    }

    .am-edit-gallery-preview:hover .am-edit-gallery-open,
    .am-edit-gallery-preview:focus-visible .am-edit-gallery-open {
        opacity: 1;
    }

    .am-edit-gallery-remove {
        position: absolute;
        top: .5rem;
        right: .5rem;
        z-index: 2;
        display: grid;
        min-width: 2.5rem;
        min-height: 2.5rem;
        place-items: center;
        border-radius: .6rem;
        background: rgb(255 255 255 / 92%);
        box-shadow: 0 1px 3px rgb(0 0 0 / 14%);
    }

    .am-edit-gallery-details {
        display: grid;
        gap: .5rem;
        padding: .75rem;
    }

    .am-edit-gallery-filename {
        overflow: hidden;
        color: #161513;
        font-size: .75rem;
        font-weight: 500;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .am-edit-gallery-badges {
        display: flex;
        flex-wrap: wrap;
        gap: .375rem;
    }

    .am-edit-gallery-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #f4f4f5;
        padding: .2rem .5rem;
        color: #6b6b6b;
        font-size: .6875rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .am-edit-gallery-badge.is-cover {
        background: #fef2f2;
        color: #9e1b26;
    }

    .am-edit-gallery-badge.is-ready {
        background: #eef3ef;
        color: #3f5d45;
    }

    .am-edit-gallery-badge.is-failed {
        background: #fef2f2;
        color: #b91c1c;
    }

    .am-edit-gallery-help {
        border-left: 2px solid #ded8d2;
        padding-left: .75rem;
    }

    .dark .am-edit-gallery-title,
    .dark .am-edit-gallery-empty-title {
        color: #fafafa;
    }

    .dark .am-edit-gallery-copy,
    .dark .am-edit-gallery-empty-copy,
    .dark .am-edit-gallery-help {
        color: #a1a1aa;
    }

    .dark .am-edit-gallery-empty,
    .dark .am-edit-gallery-preview {
        border-color: #3f3f46;
        background: #27272a;
    }

    .dark .am-edit-gallery-card {
        border-color: #3f3f46;
        background: #18181b;
    }

    .dark .am-edit-gallery-filename {
        color: #d4d4d8;
    }

    @media (max-width: 640px) {
        .am-edit-gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .625rem;
        }

        .am-edit-gallery-details {
            padding: .625rem;
        }

        .am-edit-gallery-open {
            opacity: 1;
        }
    }

    @media (max-width: 380px) {
        .am-edit-gallery-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
