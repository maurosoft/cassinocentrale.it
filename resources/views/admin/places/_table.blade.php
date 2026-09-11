<div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Categoria / Tipo</th>
                    <th class="px-4 py-3">Stato</th>
                    <th class="px-4 py-3 text-right">Azioni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cream-200">
                @forelse ($items as $place)
                    <tr class="hover:bg-cream-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-ink">{{ $place->name }}</p>
                            <p class="text-xs text-ink-soft">{{ \Illuminate\Support\Str::limit($place->description, 50) }}</p>
                        </td>
                        <td class="px-4 py-3 text-ink-light">
                            {{ $place->is_convention ? $place->typeLabel() : $place->categoryLabel() }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($place->is_active)
                                <span class="rounded-full bg-sage-100 px-2.5 py-1 text-xs font-medium text-sage-700">Attiva</span>
                            @else
                                <span class="rounded-full bg-cream-200 px-2.5 py-1 text-xs font-medium text-ink-light">Nascosta</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.places.edit', $place) }}" class="rounded-lg p-2 text-ink-light hover:bg-cream-100"><x-icon name="edit" class="h-4 w-4"/></a>
                                <form method="POST" action="{{ route('admin.places.destroy', $place) }}" onsubmit="return confirm('Eliminare «{{ $place->name }}»?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-ink-soft">{{ $empty }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
