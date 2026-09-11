@extends('layouts.admin')

@section('title', 'Utenti')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-ink-light">{{ $users->count() }} utenti</p>
        <a href="{{ route('admin.users.create') }}" class="btn-primary !py-2.5"><x-icon name="plus" class="h-4 w-4"/> Nuovo utente</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr><th class="px-4 py-3">Nome</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Ruolo</th><th class="px-4 py-3 text-right">Azioni</th></tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @foreach ($users as $user)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3 font-medium text-ink">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-ink-light">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium @class([
                                    'bg-clay-50 text-clay-700' => $user->role === 'superadmin',
                                    'bg-sage-100 text-sage-700' => $user->role === 'reception',
                                    'bg-cream-200 text-ink-light' => $user->role === 'editor',
                                ])">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg p-2 text-ink-light hover:bg-cream-100"><x-icon name="edit" class="h-4 w-4"/></a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Eliminare {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
