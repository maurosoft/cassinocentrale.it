@extends('layouts.admin')

@section('title', 'Modifica servizio')

@section('content')
    <a href="{{ route('admin.services.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna ai servizi</a>
    <form method="POST" action="{{ route('admin.services.update', $service) }}">
        @method('PUT')
        @include('admin.services._form')
    </form>
@endsection
