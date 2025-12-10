@extends('layouts.app')

@section('title', 'Modifier Client - Mercedes-Benz Bot')
@section('page-title', 'Modifier le Client')

@section('content')
<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('dashboard.clients.show', $client->id) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour au détail du client
    </a>
</div>

<!-- Edit Form -->
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Modifier les informations du client</h2>

    <form action="{{ route('dashboard.clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nom Complet Réel -->
            <div>
                <label for="client_full_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom complet (réel)
                </label>
                <input type="text"
                       name="client_full_name"
                       id="client_full_name"
                       value="{{ old('client_full_name', $client->client_full_name) }}"
                       class="input-field @error('client_full_name') border-red-500 @enderror"
                       placeholder="Jean Dupont">
                @error('client_full_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Nom saisi manuellement par le client</p>
            </div>

            <!-- Nom Profil WhatsApp -->
            <div>
                <label for="whatsapp_profile_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom profil WhatsApp
                </label>
                <input type="text"
                       name="whatsapp_profile_name"
                       id="whatsapp_profile_name"
                       value="{{ old('whatsapp_profile_name', $client->whatsapp_profile_name) }}"
                       class="input-field bg-gray-50 @error('whatsapp_profile_name') border-red-500 @enderror"
                       readonly>
                @error('whatsapp_profile_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Mis à jour automatiquement depuis WhatsApp</p>
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Numéro de téléphone <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="phone_number"
                       id="phone_number"
                       value="{{ old('phone_number', $client->phone_number) }}"
                       required
                       class="input-field @error('phone_number') border-red-500 @enderror">
                @error('phone_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email"
                       name="email"
                       id="email"
                       value="{{ old('email', $client->email) }}"
                       class="input-field @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- VIN -->
            <div>
                <label for="vin" class="block text-sm font-medium text-gray-700 mb-2">
                    Numéro VIN
                </label>
                <input type="text"
                       name="vin"
                       id="vin"
                       value="{{ old('vin', $client->vin) }}"
                       class="input-field @error('vin') border-red-500 @enderror">
                @error('vin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Carte VIP -->
            <div>
                <label for="carte_vip" class="block text-sm font-medium text-gray-700 mb-2">
                    Numéro de carte VIP
                </label>
                <input type="text"
                       name="carte_vip"
                       id="carte_vip"
                       value="{{ old('carte_vip', $client->carte_vip) }}"
                       class="input-field @error('carte_vip') border-red-500 @enderror">
                @error('carte_vip')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Client -->
            <div>
                <label for="is_client" class="block text-sm font-medium text-gray-700 mb-2">
                    Statut client
                </label>
                <select name="is_client"
                        id="is_client"
                        class="input-field @error('is_client') border-red-500 @enderror">
                    <option value="">Non défini</option>
                    <option value="1" {{ old('is_client', $client->is_client) === true ? 'selected' : '' }}>
                        Client Mercedes
                    </option>
                    <option value="0" {{ old('is_client', $client->is_client) === false ? 'selected' : '' }}>
                        Non-client
                    </option>
                </select>
                @error('is_client')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('dashboard.clients.show', $client->id) }}"
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Annuler
            </a>
            <button type="submit"
                    class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
