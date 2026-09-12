@extends('layouts.admin')

@section('title', 'Log WhatsApp')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-ink-light">Storico dei messaggi WhatsApp inviati (con provider ed esito).</p>
        <a href="{{ route('admin.whatsapp.index') }}" class="btn-outline !py-2">Configura WhatsApp</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">Numero</th>
                        <th class="px-4 py-3">Messaggio</th>
                        <th class="px-4 py-3">Esito</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3 text-ink-light">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-ink-soft">{{ $log->provider }}</td>
                            <td class="px-4 py-3 text-ink">{{ $log->to }}</td>
                            <td class="px-4 py-3 text-ink-light">{{ \Illuminate\Support\Str::limit($log->message, 50) }}</td>
                            <td class="px-4 py-3">
                                @if ($log->status === 'sent')
                                    <span class="rounded-full bg-sage-100 px-2.5 py-1 text-xs font-medium text-sage-700">Inviato</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700" title="{{ $log->response }}">Errore</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-ink-soft">Nessun messaggio WhatsApp inviato finora.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
