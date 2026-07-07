<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Termii MCP &middot; Send SMS &amp; WhatsApp from your AI assistant</title>
        <meta name="description" content="Termii MCP is a hosted Model Context Protocol server that lets Claude, ChatGPT and other AI clients send SMS and WhatsApp messages, run campaigns and manage contacts through your own Termii account.">

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Termii MCP">
        <meta property="og:title" content="Termii MCP &middot; Send SMS &amp; WhatsApp from your AI assistant">
        <meta property="og:description" content="A hosted MCP server that lets Claude, ChatGPT and other AI clients send SMS and WhatsApp, run campaigns and manage contacts through your own Termii account.">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('og.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Termii MCP &middot; Send SMS &amp; WhatsApp from your AI assistant">
        <meta name="twitter:description" content="A hosted MCP server that lets Claude, ChatGPT and other AI clients send SMS and WhatsApp through your own Termii account.">
        <meta name="twitter:image" content="{{ asset('og.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-200 antialiased selection:bg-emerald-400/30">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="/" class="flex items-center gap-2 font-semibold text-white">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500 text-slate-950">T</span>
                Termii&nbsp;MCP
            </a>
            <nav class="flex items-center gap-6 text-sm text-slate-400">
                <a href="#tools" class="hidden hover:text-white sm:inline">Capabilities</a>
                <a href="#tutorial" class="hidden hover:text-white sm:inline">Tutorial</a>
                <a href="#connect" class="rounded-lg bg-white/10 px-4 py-2 font-medium text-white hover:bg-white/20">Connect</a>
            </nav>
        </header>

        <section class="mx-auto max-w-6xl px-6 pt-12 pb-16 text-center sm:pt-20">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-4 py-1.5 text-xs font-medium text-emerald-300">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Model Context Protocol server
            </span>
            <h1 class="mx-auto mt-6 max-w-3xl text-balance text-4xl font-bold tracking-tight text-white sm:text-6xl">
                Give your AI assistant the power to send SMS &amp; WhatsApp
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-400">
                Termii MCP connects Claude, ChatGPT and any MCP-compatible client to your
                <a href="https://termii.com" class="text-emerald-300 underline-offset-4 hover:underline">Termii</a>
                account. Send messages, manage phonebooks and run campaigns, all in natural language.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="#connect" class="w-full rounded-xl bg-emerald-500 px-6 py-3 font-semibold text-slate-950 transition hover:bg-emerald-400 sm:w-auto">
                    Connect it now
                </a>
                <a href="#tutorial" class="w-full rounded-xl border border-white/15 px-6 py-3 font-semibold text-white transition hover:bg-white/5 sm:w-auto">
                    Watch the tutorial
                </a>
            </div>
            <p class="mt-4 text-sm text-slate-500">Bring your own Termii API key &middot; 22 tools &middot; No sign-up here</p>
        </section>

        <section id="tutorial" class="mx-auto max-w-4xl px-6 pb-20">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-white sm:text-3xl">See it in action</h2>
                <p class="mt-2 text-slate-400">A quick walkthrough of connecting and sending your first message.</p>
            </div>
            {{-- Replace VIDEO_ID below with your YouTube video id (or swap the whole iframe for Vimeo / a self-hosted <video>). --}}
            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-slate-900 shadow-2xl shadow-emerald-500/5" style="aspect-ratio: 16 / 9;">
                <iframe
                    class="absolute inset-0 h-full w-full"
                    src="https://www.youtube-nocookie.com/embed/VIDEO_ID"
                    title="Termii MCP tutorial"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
                </iframe>
            </div>
        </section>

        <section id="tools" class="mx-auto max-w-6xl px-6 pb-20">
            <div class="mb-10 text-center">
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Everything Termii can do</h2>
                <p class="mt-2 text-slate-400">The complete Termii API, exposed as tools your assistant can call.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-5">
                @foreach ($features as $feature)
                    <div class="flex w-full flex-col rounded-2xl border border-white/10 bg-white/3 p-6 transition hover:border-emerald-400/30 hover:bg-white/5 sm:w-[calc(50%-0.625rem)] lg:w-[calc(33.333%-0.834rem)]">
                        <h3 class="text-lg font-semibold text-white">{!! $feature['title'] !!}</h3>
                        <p class="mt-2 flex-1 text-sm text-slate-400">{!! $feature['desc'] !!}</p>
                        <p class="mt-4 font-mono text-xs leading-relaxed text-emerald-300/80">{{ $feature['tools'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="connect" class="mx-auto max-w-2xl px-6 pb-24">
            <form data-connect data-mcp-base="{{ $mcpUrl }}" class="rounded-3xl border border-white/10 bg-linear-to-b from-white/6 to-transparent p-8 text-center sm:p-10">
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Connect in two clicks</h2>
                <p class="mx-auto mt-2 max-w-md text-slate-400">
                    Paste your
                    <a href="https://accounts.termii.com" class="text-emerald-300 underline-offset-4 hover:underline">Termii API key</a>,
                    choose your assistant, and connect.
                </p>

                <div class="mx-auto mt-6 grid max-w-md gap-4 text-left">
                    <div>
                        <label for="api-key" class="mb-2 block text-sm font-medium text-slate-300">Your Termii API key</label>
                        <input id="api-key" type="text" autocomplete="off" autocapitalize="off" spellcheck="false"
                               placeholder="TL••••••••••••••••••••"
                               class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 font-mono text-sm text-white placeholder:text-slate-600 focus:border-emerald-400/50 focus:outline-none">
                    </div>

                    <div>
                        <label for="assistant" class="mb-2 block text-sm font-medium text-slate-300">Assistant</label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <div class="relative sm:flex-1">
                                <select id="assistant"
                                        class="w-full appearance-none rounded-xl border border-white/10 bg-slate-950 px-4 py-3 pr-10 text-sm text-white focus:border-emerald-400/50 focus:outline-none">
                                    <option value="claude">Claude</option>
                                    <option value="chatgpt">ChatGPT</option>
                                </select>
                                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <button type="submit" data-connect-btn
                                    class="rounded-xl bg-emerald-500 px-6 py-3 font-semibold text-slate-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60">
                                Connect to Claude
                            </button>
                        </div>
                    </div>
                </div>

                <div data-url-row hidden class="mx-auto mt-4 flex max-w-md items-center gap-2 rounded-xl border border-white/10 bg-slate-950 p-2 pl-4">
                    <code data-connector-url class="flex-1 overflow-x-auto whitespace-nowrap text-left font-mono text-sm text-emerald-300"></code>
                    <button type="button" data-copy-connector class="shrink-0 rounded-lg bg-white/10 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-white/20">Copy</button>
                </div>

                <p class="mx-auto mt-5 max-w-md text-xs text-slate-500">
                    Your key is encrypted into the link, so it never appears in plain text and only this server can
                    read it. Connecting to Claude opens a prefilled connector dialog; for ChatGPT the link is copied so
                    you can paste it into Settings &rarr; Connectors. Your key is never stored here.
                </p>
            </form>
        </section>

        <div id="toast" class="pointer-events-none fixed inset-x-0 bottom-6 z-50 mx-auto hidden w-fit rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-medium text-slate-950 shadow-lg"></div>

        <footer class="border-t border-white/10">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm text-slate-500 sm:flex-row">
                <p>&copy; {{ date('Y') }} Termii MCP. Not affiliated with Termii.</p>
                <div class="flex items-center gap-6">
                    <a href="https://github.com/zeevx/lara-termii" class="hover:text-slate-300">Built with lara-termii</a>
                </div>
            </div>
        </footer>
    </body>
</html>
