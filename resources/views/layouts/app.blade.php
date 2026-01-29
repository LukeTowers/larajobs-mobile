<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
        <flux:header container="header" class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <flux:brand href="#" name="LaraJobs" class="h-10! max-lg:h-14!"/>

            <flux:spacer/>

            <flux:icon.bars-3/>
        </flux:header>
        {{ $slot }}

        @persist('toast')
            <flux:toast position="top right"/>
        @endpersist
        @fluxScripts
    </body>
</html>
