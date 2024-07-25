<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class EventController extends Controller
{
    public function index(Request $request)
    {

        $sort_by = $request->get('sort_by', 'id');
        $order = $request->get('order', 'asc');


        $validSortFields = ['id', 'name', 'event_date', 'venue_name'];
    

        $sort_by = in_array($sort_by, $validSortFields) ? $sort_by : 'id';
    

        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';

       
        $events = Event::with('venue')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->select('events.*', 'venues.name as venue_name')
            ->orderBy($sort_by === 'venue_name' ? 'venues.name' : 'events.'.$sort_by, $order)
            ->paginate(10);

       
        return view('events.index', compact('events', 'sort_by', 'order'));
    }


    public function create()
    {
        $venues = Venue::all();
        return view('events.create', compact('venues'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'poster' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'event_date' => 'required|date',
            'venue_id' => 'required|exists:venues,id',
        ]);

        
        $path = $request->file('poster')->store('posters', 'public');
        $img = Image::make(storage_path("app/public/{$path}"));

     
        if ($img->width() < 400 || $img->height() < 400) {
            return redirect()->back()->withErrors(['poster' => 'The image must be at least 400x400 pixels.']);
        }

     
        if ($img->width() > 800) {
            $img->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $img->save();

        Event::create([
            'name' => $request->name,
            'poster' => $path,
            'event_date' => $request->event_date,
            'venue_id' => $request->venue_id,
        ]);

        return redirect()->route('events.index');
    }

    public function edit(Event $event)
    {
        $venues = Venue::all();
        return view('events.edit', compact('event', 'venues'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'poster' => 'image|mimes:jpeg,png,jpg|max:2048',
            'event_date' => 'required|date',
            'venue_id' => 'required|exists:venues,id',
        ]);

       
        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $img = Image::make(storage_path("app/public/{$path}"));

          
            if ($img->width() < 400 || $img->height() < 400) {
                return redirect()->back()->withErrors(['poster' => 'The image must be at least 400x400 pixels.']);
            }

          
            if ($img->width() > 800) {
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $img->save();
            $event->update(['poster' => $path]);
        }

        $event->update($request->only('name', 'event_date', 'venue_id'));

        return redirect()->route('events.index');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index');
    }


}
