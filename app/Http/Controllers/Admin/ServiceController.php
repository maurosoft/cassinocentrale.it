<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::ordered()->get();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        $service = new Service();

        return view('admin.services.create', compact('service'));
    }

    public function store(Request $request): RedirectResponse
    {
        Service::create($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Servizio creato.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Servizio aggiornato.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Servizio eliminato.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_extra_cost'] = $request->boolean('is_extra_cost');
        $data['amount'] = $data['amount'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
