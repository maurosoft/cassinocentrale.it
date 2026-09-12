@extends('layouts.admin')

@section('title', 'Calendario')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.calendar.index', ['month' => $prevMonth]) }}" class="btn-outline !px-3 !py-2"><x-icon name="arrow-left" class="h-4 w-4"/></a>
            <h2 class="font-serif text-xl text-ink">{{ $month->translatedFormat('F Y') }}</h2>
            <a href="{{ route('admin.calendar.index', ['month' => $nextMonth]) }}" class="btn-outline !px-3 !py-2"><x-icon name="arrow-left" class="h-4 w-4 rotate-180"/></a>
        </div>
        <div class="flex items-center gap-3 text-xs text-ink-light">
            <span class="inline-flex items-center gap-1"><span class="h-3 w-3 rounded bg-sage-200"></span> Confermata</span>
            <span class="inline-flex items-center gap-1"><span class="h-3 w-3 rounded bg-amber-200"></span> Richiesta</span>
            <span class="inline-flex items-center gap-1"><span class="h-3 w-3 rounded bg-cream-300"></span> Chiuso</span>
            <a href="{{ route('admin.closures.index') }}" class="btn-primary !py-2 !px-3">Gestisci chiusure</a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-cream-300 bg-white">
        <table class="min-w-max border-collapse text-center text-xs">
            <thead>
                <tr class="bg-cream-50">
                    <th class="sticky left-0 z-10 bg-cream-50 px-3 py-2 text-left font-medium text-ink-soft">Camera</th>
                    @foreach ($days as $day)
                        <th class="px-1 py-2 font-medium {{ $day->isWeekend() ? 'text-clay-600' : 'text-ink-soft' }}" style="min-width:34px">
                            <span class="block">{{ $day->translatedFormat('D')[0] }}</span>
                            <span class="block">{{ $day->format('d') }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rooms as $room)
                    <tr class="border-t border-cream-200">
                        <td class="sticky left-0 z-10 whitespace-nowrap bg-white px-3 py-2 text-left font-medium text-ink">{{ $room->number_name }}</td>
                        @foreach ($days as $day)
                            @php($cell = $grid[$room->id][$day->format('Y-m-d')] ?? null)
                            <td class="border-l border-cream-100 p-0.5">
                                @if ($cell)
                                    <div class="group relative flex h-8 items-center justify-center rounded
                                        @class([
                                            'bg-sage-200 text-sage-800' => $cell['type'] === 'booked',
                                            'bg-amber-200 text-amber-900' => $cell['type'] === 'request',
                                            'bg-cream-300 text-ink-soft' => $cell['type'] === 'closed',
                                        ])" title="{{ $cell['label'] }}">
                                        <span class="truncate px-1" style="max-width:30px">{{ \Illuminate\Support\Str::of($cell['label'])->substr(0, 2) }}</span>
                                    </div>
                                @else
                                    <div class="h-8 rounded bg-cream-50"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-ink-soft">Passa il mouse su una cella per vedere il nome dell'ospite o il motivo della chiusura. Le celle vuote sono libere.</p>
@endsection
