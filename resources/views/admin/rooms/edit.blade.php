@extends('layouts.admin')

@section('title', 'Modifica camera '.$room->number_name)

@section('content')
    <a href="{{ route('admin.rooms.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna alle camere</a>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.rooms._form')
    </form>
@endsection
