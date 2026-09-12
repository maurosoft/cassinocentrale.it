<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('bookings')->orderBy('first_name')->orderBy('last_name');

        if ($search = $request->query('q')) {
            $query->where(fn ($q) => $q
                ->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%"));
        }

        $customers = $query->paginate(30)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['bookings' => fn ($q) => $q->with('rooms.room')->latest('check_in')]);

        return view('admin.customers.show', compact('customer'));
    }
}
