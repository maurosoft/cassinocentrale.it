@csrf
<div class="max-w-2xl space-y-5 rounded-2xl border border-cream-300 bg-white p-5">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-ink">Nome ospite *</label>
            <input name="guest_name" value="{{ old('guest_name', $review->guest_name) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink">Voto *</label>
            <select name="rating" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                @for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}" @selected(old('rating', $review->rating) == $i)>{{ str_repeat('★', $i) }} ({{ $i }})</option>@endfor
            </select>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-ink">Titolo</label>
        <input name="title" value="{{ old('title', $review->title) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-ink">Commento</label>
        <textarea name="comment" rows="3" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('comment', $review->comment) }}</textarea>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-ink">Fonte</label>
            <input name="source" value="{{ old('source', $review->source) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Google">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink">Data soggiorno</label>
            <input name="stay_date" type="date" value="{{ old('stay_date', optional($review->stay_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink">Ordine</label>
            <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $review->sort_order) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
        </div>
    </div>
    <label class="flex items-center gap-2 text-sm text-ink">
        <input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $review->is_visible)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> Mostra sul sito
    </label>
</div>

<div class="mt-6 flex max-w-2xl items-center justify-end gap-3">
    <a href="{{ route('admin.reviews.index') }}" class="btn-ghost">Annulla</a>
    <button type="submit" class="btn-primary">Salva recensione</button>
</div>
