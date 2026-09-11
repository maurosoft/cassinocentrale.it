@extends('layouts.admin')

@section('title', 'Camere')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-ink-light">{{ $rooms->count() }} camere</p>
        <a href="{{ route('admin.rooms.create') }}" class="btn-primary !py-2.5"><x-icon name="plus" class="h-4 w-4"/> Nuova camera</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-4 py-3">Camera</th>
                        <th class="px-4 py-3">Prezzo</th>
                        <th class="px-4 py-3">Ospiti</th>
                        <th class="px-4 py-3">Stato</th>
                        <th class="px-4 py-3 text-right">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @foreach ($rooms as $room)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($room->coverImage(), ['http','/']) ? $room->coverImage() : asset($room->coverImage()) }}" alt="" class="h-12 w-16 rounded-lg object-cover">
                                    <div>
                                        <p class="font-medium text-ink">{{ $room->number_name }}@if ($room->name) · {{ $room->name }}@endif</p>
                                        <p class="text-xs text-ink-soft">{{ \Illuminate\Support\Str::limit($room->short_description, 40) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">€{{ number_format($room->base_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $room->max_guests }}</td>
                            <td class="px-4 py-3">
                                @if ($room->is_active)
                                    <span class="rounded-full bg-sage-100 px-2.5 py-1 text-xs font-medium text-sage-700">Attiva</span>
                                @else
                                    <span class="rounded-full bg-cream-200 px-2.5 py-1 text-xs font-medium text-ink-light">Non attiva</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="rounded-lg p-2 text-ink-light hover:bg-cream-100" title="Modifica"><x-icon name="edit" class="h-4 w-4"/></a>
                                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Eliminare la camera {{ $room->number_name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" title="Elimina"><x-icon name="trash" class="h-4 w-4"/></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
