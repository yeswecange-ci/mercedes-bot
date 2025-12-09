<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mercedes-Benz Bot Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-slate-50 antialiased min-h-screen" x-data="{ sidebarOpen: false }">
    @auth
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <!-- Logo -->
        <div class="flex items-center justify-between h-20 px-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-md">
                    <img src="{{ asset('images/logomercedes.png') }}" alt="Mercedes-Benz" class="h-7 w-7 object-contain">
                </div>
                <div>
                    <span class="text-base font-bold text-slate-800">Mercedes-Benz</span>
                    <p class="text-xs text-slate-500 font-medium">Bot Dashboard</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation - Scrollable -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
            <a href="{{ route('dashboard') }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard')) bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('dashboard.pending') }}"
               class="group relative flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.pending')) bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif"
               x-data="{ pendingCount: {{ \App\Models\Conversation::where('status', 'transferred')->whereNull('agent_id')->count() }} }"
               x-init="setInterval(() => {
                   fetch('/api/dashboard/pending-count', {
                       headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('token') || '') }
                   }).then(r => r.json()).then(data => pendingCount = data.count).catch(() => {});
               }, 5000)">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.pending')) @else group-hover:scale-110 transition-transform @endif" :class="{ 'animate-pulse': pendingCount > 0 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="flex-1">En attente agent</span>
                <span x-show="pendingCount > 0"
                      x-transition
                      class="px-2 py-0.5 text-xs font-bold rounded-full @if(request()->routeIs('dashboard.pending')) bg-white/20 @else bg-gradient-to-r from-orange-500 to-red-500 text-white @endif shadow-sm"
                      x-text="pendingCount"></span>
            </a>

            <a href="{{ route('dashboard.active') }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.active')) bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md shadow-emerald-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.active')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Conversations actives</span>
            </a>

            <a href="{{ route('dashboard.conversations') }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.conversations') || request()->routeIs('dashboard.show')) bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.conversations') || request()->routeIs('dashboard.show')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <span>Toutes les conversations</span>
            </a>

            <a href="{{ route('dashboard.statistics') }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.statistics')) bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow-md shadow-violet-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.statistics')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Statistiques</span>
            </a>

            <a href="{{ route('dashboard.search') }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.search')) bg-gradient-to-r from-cyan-500 to-sky-500 text-white shadow-md shadow-cyan-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.search')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Recherche</span>
            </a>

            <a href="{{ route('dashboard.clients.index') }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.clients.*')) bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow-md shadow-indigo-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.clients.*')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Clients</span>
            </a>

            @if(auth()->user()->canManageUsers())
            <div class="pt-3 mt-3 border-t border-slate-200">
                <p class="px-3 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Administration</p>
                <a href="{{ route('dashboard.users.index') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 @if(request()->routeIs('dashboard.users.*')) bg-gradient-to-r from-fuchsia-500 to-pink-500 text-white shadow-md shadow-fuchsia-500/30 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                    <svg class="w-5 h-5 mr-3 @if(request()->routeIs('dashboard.users.*')) @else group-hover:scale-110 transition-transform @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Utilisateurs</span>
                </a>
            </div>
            @endif
        </nav>

        <!-- User Profile -->
        <div class="border-t border-slate-200 bg-gradient-to-r from-slate-50 to-white flex-shrink-0" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center w-full px-4 py-3 text-sm hover:bg-slate-100/50 transition-all duration-200 group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition-shadow">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="ml-3 text-left flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-500 font-medium">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="userMenuOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="px-3 pb-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden"></div>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Top navbar -->
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm">
            <div class="flex items-center justify-between h-16 px-6">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1 lg:ml-0 ml-4">
                    <h1 class="text-xl font-bold text-slate-800 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" class="relative text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(isset($activeCount) && $activeCount > 0)
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <!-- Notifications Dropdown -->
                        <div x-show="notifOpen"
                             @click.away="notifOpen = false"
                             x-cloak
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-gray-200">
                            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @if(isset($activeCount) && $activeCount > 0)
                                <a href="{{ route('dashboard.active') }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $activeCount }} conversation(s) active(s)</p>
                                            <p class="text-xs text-gray-500 mt-1">Cliquez pour voir les détails</p>
                                        </div>
                                    </div>
                                </a>
                                @else
                                <div class="px-4 py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Aucune notification</p>
                                </div>
                                @endif
                            </div>
                            <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                <a href="{{ route('dashboard') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    Voir le tableau de bord →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6 max-w-[1600px] mx-auto">
            @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center shadow-sm"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="flex-1 font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="flex-shrink-0 text-emerald-600 hover:text-emerald-800 p-1 rounded-lg hover:bg-emerald-100 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl flex items-center shadow-sm"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="flex-1 font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="flex-shrink-0 text-red-600 hover:text-red-800 p-1 rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
    @else
    @yield('content')
    @endauth

    @stack('scripts')
</body>
</html>
