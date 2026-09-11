@csrf
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Dati principali</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Numero / nome camera *</label>
                    <input name="number_name" value="{{ old('number_name', $room->number_name) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="102">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Nome descrittivo</label>
                    <input name="name" value="{{ old('name', $room->name) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Matrimoniale Dante">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Prezzo base (€/notte) *</label>
                    <input name="base_price" type="number" step="0.01" min="0" value="{{ old('base_price', $room->base_price) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Ospiti massimi *</label>
                    <input name="max_guests" type="number" min="1" max="10" value="{{ old('max_guests', $room->max_guests) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-ink">Descrizione breve</label>
                    <input name="short_description" value="{{ old('short_description', $room->short_description) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Accogliente camera matrimoniale...">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-ink">Descrizione completa</label>
                    <textarea name="description" rows="5" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('description', $room->description) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-ink">Regole (check-in/out, ecc.)</label>
                    <textarea name="rules" rows="2" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('rules', $room->rules) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Foto --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Foto</h2>
            @if (! empty($room->images))
                <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                    @foreach ($room->images as $img)
                        <label class="relative block cursor-pointer overflow-hidden rounded-lg border border-cream-300">
                            <img src="{{ \Illuminate\Support\Str::startsWith($img, ['http','/']) ? $img : asset($img) }}" alt="" class="aspect-square w-full object-cover">
                            <span class="absolute inset-x-0 bottom-0 flex items-center gap-1 bg-black/50 px-2 py-1 text-xs text-white">
                                <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="rounded"> elimina
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
            <div class="mt-4">
                <label class="block text-sm font-medium text-ink">Aggiungi foto</label>
                <input type="file" name="images[]" accept="image/*" multiple class="mt-1 block w-full text-sm text-ink-light file:mr-3 file:rounded-lg file:border-0 file:bg-clay-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-clay-700">
                <p class="mt-1 text-xs text-ink-soft">Puoi caricare più foto insieme (JPG/PNG/WEBP, max 6 MB l'una).</p>
            </div>
        </div>
    </div>

    {{-- Colonna laterale --}}
    <div class="space-y-5">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Opzioni</h2>
            <label class="mt-4 flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $room->is_active)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> Camera attiva (visibile sul sito)
            </label>
            <label class="mt-3 flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="has_kitchenette" value="1" @checked(old('has_kitchenette', $room->has_kitchenette)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> Ha l'angolo cottura
            </label>
        </div>

        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Servizi</h2>
            <p class="mt-1 text-xs text-ink-soft">Spunta i servizi inclusi. Il costo extra è opzionale.</p>
            <div class="mt-3 space-y-2">
                @php($selected = old('services', $room->services->pluck('id')->all()))
                @foreach ($services as $service)
                    <div class="flex items-center justify-between gap-2">
                        <label class="flex items-center gap-2 text-sm text-ink">
                            <input type="checkbox" name="services[]" value="{{ $service->id }}" @checked(in_array($service->id, $selected)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                            {{ $service->name }}
                        </label>
                        <input name="extra_cost[{{ $service->id }}]" type="number" step="0.01" min="0"
                               value="{{ old('extra_cost.'.$service->id, optional($room->services->firstWhere('id', $service->id))->pivot->extra_cost ?? '') }}"
                               class="w-20 rounded-lg border-cream-300 text-xs focus:border-clay-500 focus:ring-clay-500" placeholder="€ extra">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.rooms.index') }}" class="btn-ghost">Annulla</a>
    <button type="submit" class="btn-primary">Salva camera</button>
</div>
