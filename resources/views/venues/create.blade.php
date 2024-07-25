@extends('layouts.app')

@section('content')
    @if(auth()->user() && auth()->user()->isAdmin()) 
        <h1 style="margin-bottom: 30px;">Create Venue</h1>

        <form action="{{ route('venues.store') }}" method="POST" style="margin-bottom: 40px;">
            @csrf
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    @else
        <p>You are not an admin</p>
    @endif
@endsection
