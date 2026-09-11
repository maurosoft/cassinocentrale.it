<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomRequest;
use App\Models\Room;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::ordered()->withCount('bookingRooms')->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $room = new Room(['is_active' => true, 'max_guests' => 2, 'base_price' => 65]);
        $services = Service::ordered()->get();

        return view('admin.rooms.create', compact('room', 'services'));
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $room = Room::create($this->data($request));
        $this->syncServices($room, $request);
        $this->handleImages($room, $request);

        return redirect()->route('admin.rooms.index')->with('success', 'Camera creata.');
    }

    public function edit(Room $room): View
    {
        $services = Service::ordered()->get();
        $room->load('services');

        return view('admin.rooms.edit', compact('room', 'services'));
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($this->data($request));
        $this->syncServices($room, $request);
        $this->handleImages($room, $request);

        // Rimozione immagini selezionate
        if ($request->filled('remove_images')) {
            $keep = collect($room->images ?? [])->reject(fn ($img) => in_array($img, $request->input('remove_images', []), true))->values()->all();
            $room->update(['images' => $keep]);
        }

        return redirect()->route('admin.rooms.index')->with('success', 'Camera aggiornata.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Camera eliminata.');
    }

    /** Campi base della camera. */
    private function data(RoomRequest $request): array
    {
        $data = $request->safe()->only([
            'number_name', 'name', 'short_description', 'description', 'rules', 'base_price', 'max_guests',
        ]);
        $data['has_kitchenette'] = $request->boolean('has_kitchenette');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** Collega i servizi scelti, con eventuale costo extra. */
    private function syncServices(Room $room, RoomRequest $request): void
    {
        $sync = [];
        foreach ($request->input('services', []) as $serviceId) {
            $extra = $request->input("extra_cost.$serviceId");
            $sync[$serviceId] = ['extra_cost' => is_numeric($extra) ? $extra : null];
        }
        $room->services()->sync($sync);
    }

    /** Salva le nuove foto caricate e le aggiunge alla camera. */
    private function handleImages(Room $room, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $images = $room->images ?? [];
        foreach ($request->file('images') as $file) {
            $path = $file->store('rooms', 'public');
            $images[] = 'storage/'.$path;
        }
        $room->update(['images' => $images]);
    }
}
