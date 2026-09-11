@csrf
<div class="max-w-2xl space-y-5 rounded-2xl border border-cream-300 bg-white p-5">
    <div>
        <label class="block text-sm font-medium text-ink">Nome *</label>
        <input name="name" value="{{ old('name', $service->name) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Wi-Fi in fibra">
    </div>
    <div>
        <label class="block text-sm font-medium text-ink">Descrizione</label>
        <input name="description" value="{{ old('description', $service->description) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-ink">Icona (nome)</label>
            <input name="icon" value="{{ old('icon', $service->icon) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="wifi">
            <p class="mt-1 text-xs text-ink-soft">Es. wifi, coffee, tv, snowflake, bed, key.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-ink">Ordine</label>
            <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $service->sort_order) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
    </div>
    <div class="rounded-lg bg-cream-50 p-4">
        <label class="flex items-center gap-2 text-sm text-ink">
            <input type="checkbox" name="is_extra_cost" value="1" @checked(old('is_extra_cost', $service->is_extra_cost)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> Ha un costo extra
        </label>
        <div class="mt-3">
            <label class="block text-sm font-medium text-ink">Importo (€)</label>
            <input name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $service->amount) }}" class="mt-1 w-40 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
    </div>
</div>

<div class="mt-6 flex max-w-2xl items-center justify-end gap-3">
    <a href="{{ route('admin.services.index') }}" class="btn-ghost">Annulla</a>
    <button type="submit" class="btn-primary">Salva servizio</button>
</div>
