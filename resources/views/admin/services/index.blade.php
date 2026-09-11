@extends('layouts.admin')

@section('title', 'Servizi')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-ink-light">{{ $services->count() }} servizi</p>
        <a href="{{ route('admin.services.create') }}" class="btn-primary !py-2.5"><x-icon name="plus" class="h-4 w-4"/> Nuovo servizio</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-4 py-3">Servizio</th>
                        <th class="px-4 py-3">Costo extra</th>
                        <th class="px-4 py-3 text-right">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @forelse ($services as $service)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-ink">{{ $service->name }}</p>
                                <p class="text-xs text-ink-soft">{{ $service->description }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $service->is_extra_cost ? '€'.number_format($service->amount, 2, ',', '.') : 'Incluso' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="rounded-lg p-2 text-ink-light hover:bg-cream-100"><x-icon name="edit" class="h-4 w-4"/></a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Eliminare questo servizio?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-ink-soft">Nessun servizio.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
