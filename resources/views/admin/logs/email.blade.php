@extends('layouts.admin')

@section('title', 'Log email')

@section('content')
    <p class="mb-4 text-sm text-ink-light">Storico delle email inviate dal sistema (conferme, modifiche, test…).</p>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Destinatario</th>
                        <th class="px-4 py-3">Oggetto</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Esito</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3 text-ink-light">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-ink">{{ $log->to }}</td>
                            <td class="px-4 py-3 text-ink-light">{{ $log->subject }}</td>
                            <td class="px-4 py-3 text-ink-soft">{{ $log->event }}</td>
                            <td class="px-4 py-3">
                                @if ($log->status === 'sent')
                                    <span class="rounded-full bg-sage-100 px-2.5 py-1 text-xs font-medium text-sage-700">Inviata</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700" title="{{ $log->error }}">Errore</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-ink-soft">Nessuna email inviata finora.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
