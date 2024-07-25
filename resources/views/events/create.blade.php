@extends('layouts.app')

@section('content')
    @if(auth()->user() && auth()->user()->isAdmin()) 
        <h1 style="margin-bottom: 30px;">Create Event</h1>

        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 40px;">
            @csrf
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="poster">Poster</label>
                <input type="file" class="form-control-file" id="poster" name="poster" required>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="event_date">Event Date</label>
                <input type="date" class="form-control" id="event_date" name="event_date" required>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="venue_id">Venue</label>
                <select class="form-control" id="venue_id" name="venue_id" required>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    @else
        <p>You are not an admin</p> 
    @endif
@endsection
