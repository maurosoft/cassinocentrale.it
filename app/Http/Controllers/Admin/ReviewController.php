<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::ordered()->get();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create(): View
    {
        $review = new Review(['rating' => 5, 'is_visible' => true]);

        return view('admin.reviews.create', compact('review'));
    }

    public function store(Request $request): RedirectResponse
    {
        Review::create($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Recensione creata.');
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $review->update($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Recensione aggiornata.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Recensione eliminata.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:150'],
            'comment' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:50'],
            'stay_date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_visible'] = $request->boolean('is_visible');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
