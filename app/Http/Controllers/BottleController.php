<?php

namespace App\Http\Controllers;

use App\Models\Bottle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BottleController extends Controller
{
    public function __construct()
    {
        // 👇 Apply permissions similar to your other controllers
        $this->middleware('permission:bottle-list|bottle-create|bottle-delete', ['only' => ['index']]);
        $this->middleware('permission:bottle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bottle-delete', ['only' => ['destroy']]);
    }

    public function index(): View
    {
        $bottles = Bottle::orderBy('date', 'desc')->paginate(10);
        return view('bottles.index', compact('bottles'));
    }

    public function create(): View
    {
        return view('bottles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'price_per_bottle' => 'required|numeric|min:0',
        ]);

        $data['total_price'] = $data['quantity'] * $data['price_per_bottle'];

        Bottle::create($data);

        return redirect()->route('bottles.index')->with('success', 'Bottle entry added successfully.');
    }

    public function destroy(Bottle $bottle): RedirectResponse
    {
        $bottle->delete();
        return back()->with('success', 'Bottle record deleted successfully.');
    }
}
