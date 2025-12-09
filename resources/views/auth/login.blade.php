@extends('layouts.app')

@section('title', 'Connexion - Mercedes-Benz Bot Dashboard')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                Connexion
            </h2>
            <p class="text-slate-600">
                Accédez au dashboard Mercedes-Benz
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/80 backdrop-blur-xl shadow-xl shadow-slate-200/50 rounded-2xl p-8 border border-white/20">
            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf

                @if ($errors->any())
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
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
                    <input id="email"
                           name="email"
                           type="email"
                           autocomplete="email"
                           required
                           value="{{ old('email') }}"
                           class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all duration-200 text-sm placeholder:text-slate-400"
                           placeholder="votre@email.com">
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                        Mot de passe
                    </label>
                    <input id="password"
                           name="password"
                           type="password"
                           autocomplete="current-password"
                           required
                           class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all duration-200 text-sm placeholder:text-slate-400"
                           placeholder="••••••••">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember"
                           name="remember"
                           type="checkbox"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded transition-colors">
                    <label for="remember" class="ml-2 block text-sm text-slate-600 font-medium">
                        Se souvenir de moi
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    Se connecter
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
@endsection
