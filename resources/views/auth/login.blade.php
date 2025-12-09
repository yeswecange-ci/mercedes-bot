@extends('layouts.app')

@section('title', 'Connexion - Mercedes-Benz Bot Dashboard')

@section('content')
<div class="min-h-screen flex bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Left Side - Login Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-20 xl:px-24 py-12">
        <div class="w-full max-w-md">
            <!-- Logo & Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 shadow-xl shadow-blue-500/30 mb-6">
                    <img src="{{ asset('images/logomercedes.png') }}" alt="Mercedes-Benz" class="h-12 w-12 object-contain">
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                    Bienvenue
                </h2>
                <p class="text-slate-600">
                    Connectez-vous au dashboard Mercedes-Benz
                </p>
            </div>

            <!-- Login Card -->
            <div class="bg-white/70 backdrop-blur-xl shadow-xl shadow-slate-200/50 rounded-3xl p-8 border border-white/20">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl flex items-start shadow-sm">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">{{ $errors->first() }}</span>
                    </div>
                    @endif

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                            Adresse email
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   value="{{ old('email') }}"
                                   class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all duration-200 text-sm placeholder:text-slate-400"
                                   placeholder="votre@email.com">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            Mot de passe
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all duration-200 text-sm placeholder:text-slate-400"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded transition-colors">
                        <label for="remember" class="ml-2 block text-sm text-slate-600 font-medium">
                            Se souvenir de moi
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full flex justify-center items-center py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 group">
                        <svg class="h-5 w-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Se connecter</span>
                    </button>
                </form>
            </div>

            <!-- Register Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-slate-600">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                        Inscrivez-vous
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Right Side - Branding -->
    <div class="hidden lg:block relative w-0 flex-1">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700">
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <div class="max-w-lg text-white space-y-8">
                    <div class="space-y-4">
                        <h1 class="text-5xl font-bold leading-tight">
                            Mercedes-Benz<br/>Bot Dashboard
                        </h1>
                        <p class="text-xl text-blue-100 leading-relaxed">
                            Gérez et analysez toutes vos conversations WhatsApp avec simplicité et élégance.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-8">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 group">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold">1000+</div>
                            <div class="text-sm text-blue-100 font-medium">Conversations</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 group">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold">24/7</div>
                            <div class="text-sm text-blue-100 font-medium">Support</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Animated background circles -->
            <div class="absolute inset-0 overflow-hidden opacity-20">
                <div class="absolute -top-4 -right-4 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 -left-32 w-96 h-96 bg-purple-300 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-blue-300 rounded-full blur-3xl"></div>
            </div>
        </div>
    </div>
</div>
@endsection
