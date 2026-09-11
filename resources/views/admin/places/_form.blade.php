@csrf
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Informazioni</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink">Nome *</label>
                    <input name="name" value="{{ old('name', $place->name) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Descrizione</label>
                    <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('description', $place->description) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-ink">A piedi</label>
                        <input name="distance_walking" value="{{ old('distance_walking', $place->distance_walking) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="10 min a piedi">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">In bus</label>
                        <input name="distance_bus" value="{{ old('distance_bus', $place->distance_bus) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">In auto</label>
                        <input name="distance_car" value="{{ old('distance_car', $place->distance_car) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-ink">Link (sito ufficiale)</label>
                        <input name="link" value="{{ old('link', $place->link) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="https://...">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-ink">Lat</label>
                            <input name="lat" value="{{ old('lat', $place->lat) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="41.49">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink">Lng</label>
                            <input name="lng" value="{{ old('lng', $place->lng) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="13.83">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Immagine</label>
                    @if ($place->image)
                        <img src="{{ \Illuminate\Support\Str::startsWith($place->image, ['http','/']) ? $place->image : asset($place->image) }}" alt="" class="mt-2 h-24 rounded-lg object-cover">
                    @endif
                    <input type="file" name="image" accept="image/*" class="mt-2 block w-full text-sm text-ink-light file:mr-3 file:rounded-lg file:border-0 file:bg-clay-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-clay-700">
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Tipologia</h2>
            <label class="mt-4 flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="is_convention" value="1" @checked(old('is_convention', $place->is_convention)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> È un'attività locale / convenzione
            </label>

            <div class="mt-4">
                <label class="block text-sm font-medium text-ink">Categoria (luogo turistico)</label>
                <select name="category" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    <option value="">—</option>
                    @foreach (\App\Models\Place::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" @selected(old('category', $place->category) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-ink">Tipo (attività locale)</label>
                <select name="type" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    <option value="">—</option>
                    @foreach (\App\Models\Place::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(old('type', $place->type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-ink">Offerta / sconto per gli ospiti</label>
                <textarea name="convention_description" rows="3" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Sconto del 10% per i nostri ospiti...">{{ old('convention_description', $place->convention_description) }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <label class="flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $place->is_active)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> Visibile sul sito
            </label>
            <div class="mt-3">
                <label class="block text-sm font-medium text-ink">Ordine</label>
                <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $place->sort_order) }}" class="mt-1 w-28 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.places.index') }}" class="btn-ghost">Annulla</a>
    <button type="submit" class="btn-primary">Salva</button>
</div>
