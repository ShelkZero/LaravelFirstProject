@extends('layouts.app')

@section('content')
    @if(auth()->user() && auth()->user()->isAdmin()) 
        <h1 style="margin-bottom: 30px; font-size: 36px;">Edit Event</h1>

        <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $event->name }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="poster">Poster</label>
                <input type="file" class="form-control-file" id="poster" name="poster">
                @if($event->poster)
                    <div style="margin-top: 20px;">
                        <p>Current Poster:</p>
                        <img src="{{ asset('storage/' . $event->poster) }}" alt="Current Poster" style="max-width: 200px;">
                    </div>
                @endif
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="event_date">Event Date</label>
                <input type="date" class="form-control" id="event_date" name="event_date" value="{{ $event->event_date }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="venue_id">Venue</label>
                <select class="form-control" id="venue_id" name="venue_id" required>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" {{ $event->venue_id == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    @else
        <p>You are not an admin</p> 
    @endif
@endsection
