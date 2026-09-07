<x-layouts.public-layout
    title="Contact Auto Mercy | Lagos Car Dealership"
    description="Contact Auto Mercy of God Nigeria Limited by phone, email, WhatsApp, or visit either of our Lagos offices."
    :canonical="route('contact')"
    :show-floating-whats-app="false"
    :condensed-contact-footer="true"
>
    <header class="relative isolate flex min-h-[18rem] items-center justify-center overflow-hidden bg-carbon text-center text-pure-white sm:min-h-[20rem]">
        <img src="{{ asset('images/auto-mercy-hero.webp') }}" width="1792" height="1024" alt="" fetchpriority="high" class="absolute inset-0 -z-20 h-full w-full object-cover object-center">
        <div class="absolute inset-0 -z-10 bg-carbon/60" aria-hidden="true"></div>
        <div class="mx-auto w-full max-w-site px-gutter py-16">
            <h1 class="font-display text-h1 font-semibold tracking-display">Contact Auto Mercy</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">Speak with our team, ask about a vehicle, or plan a visit to either Lagos office.</p>
        </div>
    </header>

    <section id="contact-options" class="bg-pure-white py-section" aria-labelledby="contact-options-heading">
        <div class="mx-auto grid max-w-site gap-12 px-gutter lg:grid-cols-[0.75fr_1.25fr] lg:gap-20">
            <div class="max-w-lg">
                <p class="eyebrow text-mercy-red">Talk to our team</p>
                <h2 id="contact-options-heading" class="mt-3 font-display text-section-title font-semibold tracking-display text-carbon">Choose the easiest way to reach us.</h2>
                <p class="mt-5 leading-7 text-text-secondary">Call, send an email, or start a WhatsApp conversation. For vehicle enquiries, sharing the make and model helps our team assist you faster.</p>
            </div>

            <div class="border-y border-border-default">
                <a href="{{ $business['telephone_url'] }}" class="group grid min-h-24 grid-cols-[3rem_1fr_auto] items-center gap-4 border-b border-border-default py-5 transition-colors hover:text-mercy-red">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-pearl text-mercy-red">
                        <x-heroicon-o-phone class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <span><span class="block text-xs font-semibold uppercase tracking-label text-text-secondary">Phone</span><span class="mt-1 block text-base font-semibold text-carbon group-hover:text-mercy-red">0806 173 1673</span></span>
                    <x-heroicon-o-arrow-right class="h-5 w-5 text-text-secondary transition-transform group-hover:translate-x-1 group-hover:text-mercy-red" aria-hidden="true" />
                </a>

                <a href="mailto:{{ $business['email'] }}" class="group grid min-h-24 grid-cols-[3rem_1fr_auto] items-center gap-4 border-b border-border-default py-5 transition-colors hover:text-mercy-red">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-pearl text-mercy-red">
                        <x-heroicon-o-envelope class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <span class="min-w-0"><span class="block text-xs font-semibold uppercase tracking-label text-text-secondary">Email</span><span class="mt-1 block break-all text-base font-semibold text-carbon group-hover:text-mercy-red">{{ $business['email'] }}</span></span>
                    <x-heroicon-o-arrow-right class="h-5 w-5 text-text-secondary transition-transform group-hover:translate-x-1 group-hover:text-mercy-red" aria-hidden="true" />
                </a>

                <a href="{{ $whatsappUrl }}" class="group grid min-h-24 grid-cols-[3rem_1fr_auto] items-center gap-4 py-5" target="_blank" rel="noopener">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-whatsapp text-pure-white">
                        <x-icons.whatsapp class="h-5 w-5" />
                    </span>
                    <span><span class="block text-xs font-semibold uppercase tracking-label text-text-secondary">WhatsApp</span><span class="mt-1 block text-base font-semibold text-carbon transition-colors group-hover:text-available">Chat with us</span></span>
                    <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5 text-text-secondary transition-colors group-hover:text-available" aria-hidden="true" />
                </a>
            </div>
        </div>
    </section>

    <section id="locations" class="bg-pearl py-section" aria-labelledby="locations-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="grid gap-5 md:grid-cols-[1fr_0.7fr] md:items-end">
                <div class="max-w-2xl">
                    <p class="eyebrow text-mercy-red">Visit Auto Mercy</p>
                    <h2 id="locations-heading" class="mt-3 font-display text-section-title font-semibold tracking-display text-carbon">Our two Lagos offices</h2>
                </div>
                <p class="max-w-xl leading-7 text-text-secondary md:justify-self-end">Call before visiting so our team can confirm the vehicle and its current location.</p>
            </div>

            <div class="mt-10 grid gap-grid lg:grid-cols-2">
                @foreach ($locations as $location)
                    @php($mapEmbedUrl = 'https://www.google.com/maps?q='.rawurlencode($location['address']).'&output=embed')
                    <article class="overflow-hidden rounded-card border border-border-default bg-pure-white shadow-card">
                        <iframe
                            src="{{ $mapEmbedUrl }}"
                            title="Map showing Auto Mercy at {{ $location['name'] }}"
                            class="aspect-[16/10] w-full border-0 bg-metallic"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                        <div class="p-6 sm:p-7">
                            <div class="flex items-start justify-between gap-5">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Location</p>
                                    <h3 class="mt-2 text-xl font-semibold text-carbon">{{ $location['name'] }}</h3>
                                </div>
                                <a href="{{ $location['map_url'] }}" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-button border border-border-default text-carbon transition-colors hover:border-carbon hover:bg-carbon hover:text-pure-white" target="_blank" rel="noopener" aria-label="Get directions to {{ $location['name'] }}">
                                    <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5" aria-hidden="true" />
                                </a>
                            </div>
                            <address class="mt-5 flex items-start gap-3 not-italic leading-7 text-text-secondary">
                                <x-heroicon-o-map-pin class="mt-1 h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" />
                                <span>{{ $location['address'] }}</span>
                            </address>
                            <p class="mt-4 flex items-start gap-3 text-sm font-semibold text-carbon">
                                <x-heroicon-o-clock class="h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" />
                                <span>{{ $business['opening_hours_display'] }}</span>
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact-form" class="bg-pure-white py-section" aria-labelledby="contact-form-heading">
        <div class="mx-auto grid max-w-site items-start gap-12 px-gutter lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
            <div class="max-w-lg">
                <p class="eyebrow text-mercy-red">Send a message</p>
                <h2 id="contact-form-heading" class="mt-3 font-display text-section-title font-semibold tracking-display text-carbon">Tell us how we can help.</h2>
                <p class="mt-5 leading-7 text-text-secondary">Send your question or vehicle request and a member of the Auto Mercy team will follow up using the contact details you provide.</p>

                <ul class="mt-8 grid gap-4 text-sm font-medium text-carbon">
                    <li class="flex items-start gap-3"><x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-available" aria-hidden="true" /><span>General vehicle enquiries</span></li>
                    <li class="flex items-start gap-3"><x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-available" aria-hidden="true" /><span>Viewing and location questions</span></li>
                    <li class="flex items-start gap-3"><x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-available" aria-hidden="true" /><span>Delivery and reservation assistance</span></li>
                </ul>
            </div>

            <div class="rounded-card border border-border-default bg-pearl p-6 sm:p-8">
                @if (session('contact_success'))
                    <div class="mb-6 flex items-start gap-3 rounded-button bg-available/10 px-4 py-3 text-sm font-semibold text-available" role="status">
                        <x-heroicon-o-check-circle class="h-5 w-5 shrink-0" aria-hidden="true" />
                        <span>{{ session('contact_success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="grid gap-5 sm:grid-cols-2">
                    @csrf
                    <div class="hidden" aria-hidden="true"><label>Company<input name="company" tabindex="-1" autocomplete="off"></label></div>

                    <label class="text-sm font-semibold text-carbon">
                        Name
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="field-control mt-2" maxlength="120" autocomplete="name" required>
                        @error('customer_name')<span class="mt-1 block text-xs font-medium text-mercy-red">{{ $message }}</span>@enderror
                    </label>

                    <label class="text-sm font-semibold text-carbon">
                        Phone number
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="field-control mt-2" maxlength="30" inputmode="tel" autocomplete="tel" required>
                        @error('phone')<span class="mt-1 block text-xs font-medium text-mercy-red">{{ $message }}</span>@enderror
                    </label>

                    <label class="text-sm font-semibold text-carbon sm:col-span-2">
                        Email address <span class="font-normal text-text-secondary">(optional)</span>
                        <input type="email" name="email" value="{{ old('email') }}" class="field-control mt-2" maxlength="255" autocomplete="email">
                        @error('email')<span class="mt-1 block text-xs font-medium text-mercy-red">{{ $message }}</span>@enderror
                    </label>

                    <label class="text-sm font-semibold text-carbon sm:col-span-2">
                        Message
                        <textarea name="message" class="field-control mt-2 h-auto min-h-40 resize-y py-3" maxlength="2000" rows="6" required>{{ old('message') }}</textarea>
                        @error('message')<span class="mt-1 block text-xs font-medium text-mercy-red">{{ $message }}</span>@enderror
                    </label>

                    <div class="sm:col-span-2">
                        <button type="submit" class="btn-primary w-full sm:w-auto">Send message</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public-layout>
