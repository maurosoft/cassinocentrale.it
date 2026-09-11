@extends('layouts.admin')

@section('title', 'Recensioni')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-ink-light">{{ $reviews->count() }} recensioni</p>
        <a href="{{ route('admin.reviews.create') }}" class="btn-primary !py-2.5"><x-icon name="plus" class="h-4 w-4"/> Nuova recensione</a>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($reviews as $review)
            <div class="rounded-2xl border border-cream-300 bg-white p-5">
                <div class="flex items-start justify-between">
                    <div class="flex text-clay-500">
                        @for ($i = 0; $i < 5; $i++)<x-icon name="star" class="h-4 w-4 {{ $i < $review->rating ? 'fill-clay-500' : 'text-cream-300' }}"/>@endfor
                    </div>
                    <div class="flex items-center gap-2">
                        @unless ($review->is_visible)<span class="rounded-full bg-cream-200 px-2 py-0.5 text-xs text-ink-light">nascosta</span>@endunless
                        <a href="{{ route('admin.reviews.edit', $review) }}" class="rounded-lg p-1.5 text-ink-light hover:bg-cream-100"><x-icon name="edit" class="h-4 w-4"/></a>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Eliminare questa recensione?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg p-1.5 text-red-500 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/></button>
                        </form>
                    </div>
                </div>
                @if ($review->title)<p class="mt-2 font-serif text-lg text-ink">{{ $review->title }}</p>@endif
                <p class="mt-1 text-sm text-ink-light">“{{ $review->comment }}”</p>
                <p class="mt-3 text-xs text-ink-soft">— {{ $review->guest_name }}@if ($review->source) · {{ $review->source }}@endif</p>
            </div>
        @empty
            <p class="text-sm text-ink-soft">Nessuna recensione.</p>
        @endforelse
    </div>
@endsection
