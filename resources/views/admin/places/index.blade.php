@extends('layouts.admin')

@section('title', 'Luoghi & Negozi')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-ink-light">Luoghi turistici e attività locali</p>
        <a href="{{ route('admin.places.create') }}" class="btn-primary !py-2.5"><x-icon name="plus" class="h-4 w-4"/> Nuova voce</a>
    </div>

    <div class="space-y-8">
        <div>
            <h2 class="mb-3 font-serif text-lg text-ink">Luoghi turistici ({{ $attractions->count() }})</h2>
            @include('admin.places._table', ['items' => $attractions, 'empty' => 'Nessun luogo turistico.'])
        </div>
        <div>
            <h2 class="mb-3 font-serif text-lg text-ink">Negozi e attività locali ({{ $conventions->count() }})</h2>
            @include('admin.places._table', ['items' => $conventions, 'empty' => 'Nessuna attività locale.'])
        </div>
    </div>
@endsection
