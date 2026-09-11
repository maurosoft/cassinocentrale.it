@csrf
<div class="max-w-xl space-y-5 rounded-2xl border border-cream-300 bg-white p-5">
    <div>
        <label class="block text-sm font-medium text-ink">Nome *</label>
        <input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-ink">Email *</label>
        <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-ink">Ruolo *</label>
        <select name="role" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
            @foreach (\App\Models\User::ROLES as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-ink-soft">Superadmin: tutto · Reception: prenotazioni e camere · Editor: contenuti.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-ink">Password {{ $user->exists ? '(lascia vuoto per non cambiarla)' : '*' }}</label>
            <input name="password" type="password" {{ $user->exists ? '' : 'required' }} class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink">Conferma password</label>
            <input name="password_confirmation" type="password" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
    </div>
</div>

<div class="mt-6 flex max-w-xl items-center justify-end gap-3">
    <a href="{{ route('admin.users.index') }}" class="btn-ghost">Annulla</a>
    <button type="submit" class="btn-primary">Salva utente</button>
</div>
