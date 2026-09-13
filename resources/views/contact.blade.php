<x-layouts.public-layout
    title="Contact Auto Mercy | Lagos Car Dealership"
    description="Contact Auto Mercy of God Nigeria Limited by phone, email, WhatsApp, or visit either of our Lagos offices."
    :canonical="route('contact')"
    :show-floating-whats-app="false"
    :condensed-contact-footer="true"
>
    <header class="relative isolate flex min-h-[20rem] items-center justify-center overflow-hidden bg-carbon text-center text-pure-white sm:min-h-[22rem]">
        <img src="{{ asset('images/auto-mercy-1.jpg') }}" width="626" height="417" alt="" fetchpriority="high"
            class="absolute inset-0 -z-20 h-full w-full scale-105 object-cover object-center">
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(22,21,19,0.82)_0%,rgba(36,33,31,0.72)_50%,rgba(22,21,19,0.82)_100%)]"
            aria-hidden="true"></div>
        <div class="mx-auto w-full max-w-site px-gutter py-16">
            <h1 class="font-display text-[clamp(2.75rem,5vw,4rem)] font-medium leading-none tracking-display">Contact Auto Mercy</h1>
            <p class="mx-auto mt-6 max-w-3xl text-base leading-7 text-metallic sm:text-lg">Speak with our team, ask about a vehicle, or plan a visit to either Lagos office.</p>
        </div>
    </header>

    <section id="contact-options" class="bg-pearl py-section" aria-labelledby="contact-options-heading">
        <div class="mx-auto grid max-w-site gap-12 px-gutter lg:grid-cols-[0.82fr_1.18fr] lg:gap-24">
            <div class="max-w-lg">
                <p class="text-sm font-semibold uppercase tracking-label text-text-secondary">Talk to our team</p>
                <h2 id="contact-options-heading" class="mt-7 font-display text-[clamp(2.4rem,4vw,3.25rem)] font-medium leading-[1.05] tracking-display text-carbon">Choose the easiest way to reach us.</h2>
                <p class="mt-7 text-base leading-8 text-text-secondary sm:text-lg">Call, send an email, or start a WhatsApp conversation. For vehicle enquiries, sharing the make and model helps our team assist you faster.</p>
            </div>

            <div class="border-y border-border-default">
                <a href="{{ $business['telephone_url'] }}" class="group grid min-h-24 grid-cols-[3rem_1fr_auto] items-center gap-4 border-b border-border-default py-5 transition-colors hover:text-mercy-red">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-pearl text-mercy-red">
                        <x-heroicon-o-phone class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <span><span class="block text-xs font-semibold uppercase tracking-label text-text-secondary">Phone</span><span class="mt-1 block text-base font-medium text-carbon group-hover:text-mercy-red sm:text-lg">0806 173 1673</span></span>
                    <x-heroicon-o-arrow-right class="h-5 w-5 text-text-secondary transition-transform group-hover:translate-x-1 group-hover:text-mercy-red" aria-hidden="true" />
                </a>

                <a href="mailto:{{ $business['email'] }}" class="group grid min-h-24 grid-cols-[3rem_1fr_auto] items-center gap-4 border-b border-border-default py-5 transition-colors hover:text-mercy-red">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-pearl text-mercy-red">
                        <x-heroicon-o-envelope class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <span class="min-w-0"><span class="block text-xs font-semibold uppercase tracking-label text-text-secondary">Email</span><span class="mt-1 block break-all text-base font-medium text-carbon group-hover:text-mercy-red sm:text-lg">{{ $business['email'] }}</span></span>
                    <x-heroicon-o-arrow-right class="h-5 w-5 text-text-secondary transition-transform group-hover:translate-x-1 group-hover:text-mercy-red" aria-hidden="true" />
                </a>

                <a href="{{ $whatsappUrl }}" class="group grid min-h-24 grid-cols-[3rem_1fr_auto] items-center gap-4 py-5" target="_blank" rel="noopener">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-whatsapp text-pure-white">
                        <x-icons.whatsapp class="h-5 w-5" />
                    </span>
                    <span><span class="block text-xs font-semibold uppercase tracking-label text-text-secondary">WhatsApp</span><span class="mt-1 block text-base font-medium text-carbon transition-colors group-hover:text-available sm:text-lg">Chat with us</span></span>
                    <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5 text-text-secondary transition-colors group-hover:text-available" aria-hidden="true" />
                </a>
            </div>
        </div>
    </section>

    <section id="locations" class="bg-pure-white py-section" aria-labelledby="locations-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="grid gap-5 md:grid-cols-[1fr_0.7fr] md:items-end">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-label text-text-secondary">Visit Auto Mercy</p>
                    <h2 id="locations-heading" class="mt-5 font-display text-h1 font-medium tracking-display text-carbon">Our two Lagos offices</h2>
                </div>
                <p class="max-w-xl leading-7 text-text-secondary md:justify-self-end">Call before visiting so our team can confirm the vehicle and its current location.</p>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-2">
                @foreach ($locations as $location)
                    @php($mapEmbedUrl = 'https://www.google.com/maps?q='.rawurlencode($location['address']).'&output=embed')
                    <article class="overflow-hidden rounded-card border border-border-default bg-pearl">
                        <div class="relative h-52 overflow-hidden bg-metallic sm:h-56">
                            <iframe
                                src="{{ $mapEmbedUrl }}"
                                title="Map showing Auto Mercy at {{ $location['name'] }}"
                                class="h-full w-full border-0 grayscale-[0.35]"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                            <a href="{{ $location['map_url'] }}" class="absolute left-4 top-4 inline-flex min-h-10 items-center gap-2 rounded-button bg-pure-white px-4 text-sm font-medium text-carbon shadow-card transition-[transform,box-shadow] hover:-translate-y-px hover:shadow-card-hover" target="_blank" rel="noopener">
                                <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" aria-hidden="true" />
                                Open in Maps
                            </a>
                        </div>
                        <div class="p-6 sm:p-7">
                            <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Location</p>
                            <h3 class="mt-3 font-display text-2xl font-medium text-carbon">{{ $location['name'] }}</h3>
                            <address class="mt-3 not-italic leading-7 text-text-secondary">{{ $location['address'] }}</address>
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

    <section id="contact-form" class="bg-pearl py-section" aria-labelledby="contact-form-heading">
        <div class="mx-auto grid max-w-site items-start gap-12 px-gutter lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
            <div class="max-w-lg">
                <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Send a message</p>
                <h2 id="contact-form-heading" class="mt-5 font-display text-h1 font-medium tracking-display text-carbon">Tell us how we can help.</h2>
                <p class="mt-6 text-base leading-8 text-text-secondary sm:text-lg">Send your question or vehicle request and a member of the Auto Mercy team will follow up using the contact details you provide.</p>

                <ul class="mt-8 grid gap-4 text-sm font-medium text-carbon">
                    <li class="flex items-start gap-3"><x-heroicon-o-check class="mt-0.5 h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" /><span>General vehicle enquiries</span></li>
                    <li class="flex items-start gap-3"><x-heroicon-o-check class="mt-0.5 h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" /><span>Viewing and location questions</span></li>
                    <li class="flex items-start gap-3"><x-heroicon-o-check class="mt-0.5 h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" /><span>Delivery and reservation assistance</span></li>
                </ul>
            </div>

            <div class="rounded-card border border-border-default bg-pure-white p-6 sm:p-8 lg:p-10">
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
                        <button type="submit" class="btn-primary w-full rounded-full px-9 font-medium sm:w-auto">Send message</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public-layout>
