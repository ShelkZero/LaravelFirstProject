@extends('layouts.app')

@section('content')
    @if(auth()->user() && auth()->user()->isAdmin()) 
        <h1 style="margin-bottom: 30px; font-size: 36px;">Edit Venue</h1>

        <form action="{{ route('venues.update', $venue->id) }}" method="POST" style="margin-bottom: 40px;">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $venue->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    @else
        <p>You are not an admin</p> 
    @endif
@endsection
