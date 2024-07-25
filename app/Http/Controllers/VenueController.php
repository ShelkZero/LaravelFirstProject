<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use App\Services\WeatherService;

class VenueController extends Controller
{


    public function index(Request $request)
    {
        $sort_by = $request->get('sort_by', 'id');
        $order = $request->get('order', 'asc');

      
        $validSortFields = ['id', 'name'];
        $sort_by = in_array($sort_by, $validSortFields) ? $sort_by : 'id';
        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';

        $venues = Venue::orderBy($sort_by, $order)->paginate(10);

        return view('venues.index', compact('venues', 'sort_by', 'order'));
    }


    public function create()
    {
        return view('venues.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Venue::create($request->all());

        return redirect()->route('venues.index');
    }

    public function edit(Venue $venue)
    {
        return view('venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $venue->update($request->all());

        return redirect()->route('venues.index');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();
        return redirect()->route('venues.index');
    }


}
