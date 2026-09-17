<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index(): View
    {
        $attractions = Place::attractions()->ordered()->get();
        $conventions = Place::conventions()->ordered()->get();

        return view('admin.places.index', compact('attractions', 'conventions'));
    }

    public function create(): View
    {
        $place = new Place(['is_active' => true]);

        return view('admin.places.create', compact('place'));
    }

    public function store(Request $request): RedirectResponse
    {
        $place = Place::create($this->validated($request));
        $this->handleImage($place, $request);

        return redirect()->route('admin.places.index')->with('success', 'Voce creata.');
    }

    public function edit(Place $place): View
    {
        return view('admin.places.edit', compact('place'));
    }

    public function update(Request $request, Place $place): RedirectResponse
    {
        $place->update($this->validated($request));
        $this->handleImage($place, $request);

        return redirect()->route('admin.places.index')->with('success', 'Voce aggiornata.');
    }

    public function destroy(Place $place): RedirectResponse
    {
        $place->delete();

        return redirect()->route('admin.places.index')->with('success', 'Voce eliminata.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'distance_walking' => ['nullable', 'string', 'max:80'],
            'distance_car' => ['nullable', 'string', 'max:80'],
            'distance_bus' => ['nullable', 'string', 'max:80'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'link' => ['nullable', 'url', 'max:255'],
            'convention_description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);
        unset($data['image']);
        $data['is_convention'] = $request->boolean('is_convention');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function handleImage(Place $place, Request $request): void
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('places', 'public');
            $place->update(['image' => 'storage/'.$path]);
        }
    }
}
