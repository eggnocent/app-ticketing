<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'Event Ticket'))</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-3">
                <a href="{{ route('home') }}" class="text-lg font-semibold text-slate-900">
                    Event Ticket
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-1.5 text-slate-700 hover:bg-slate-100">
                                Admin
                            </a>
                        @endif

                        <span class="text-slate-500">{{ auth()->user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-md border border-slate-300 px-3 py-1.5 text-slate-700 hover:bg-slate-100">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-1.5 text-slate-700 hover:bg-slate-100">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="rounded-md bg-slate-900 px-3 py-1.5 text-white hover:bg-slate-700">
                            Daftar
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
