@extends('layouts.admin')

@section('title', 'Clienti')

@section('content')
    <form method="GET" class="mb-5">
        <input name="q" value="{{ request('q') }}" data-autofilter placeholder="Cerca per nome, email, telefono..."
               class="w-full max-w-md rounded-lg border-cream-300 text-sm focus:border-clay-500 focus:ring-clay-500">
    </form>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telefono</th>
                        <th class="px-4 py-3">Prenotazioni</th>
                        <th class="px-4 py-3 text-right">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3 font-medium text-ink">{{ $customer->fullName() }}</td>
                            <td class="px-4 py-3 text-ink-light">{{ $customer->email ?: '—' }}</td>
                            <td class="px-4 py-3 text-ink-light">{{ $customer->phone ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $customer->bookings_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-clay-600 hover:bg-cream-100"><x-icon name="eye" class="h-4 w-4"/> Scheda</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-ink-soft">Nessun cliente ancora.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.querySelector('[data-autofilter]');
            if (!input) return;
            let t;
            input.addEventListener('input', () => { clearTimeout(t); t = setTimeout(() => input.form.submit(), 450); });
        });
    </script>
@endsection
