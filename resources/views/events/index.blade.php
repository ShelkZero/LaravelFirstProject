@extends('layouts.app')

@section('content')
<div class="container">
    @if(Auth::user()->isAdmin())
        <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Create Event</a>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('events.index', ['sort_by' => 'id', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        ID
                    </a>
                </th>
                <th>
                    <a href="{{ route('events.index', ['sort_by' => 'name', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Name
                    </a>
                </th>
                <th>Poster</th>
                <th>
                    <a href="{{ route('events.index', ['sort_by' => 'event_date', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Event Date
                    </a>
                </th>
                <th>
                    <a href="{{ route('events.index', ['sort_by' => 'venue_name', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Venue
                    </a>
                </th>
                @if(Auth::user()->isAdmin()) 
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event->id }}</td>
                <td>{{ $event->name }}</td>
                <td><img src="{{ asset('storage/' . $event->poster) }}" alt="{{ $event->name }}" width="50"></td>
                <td>{{ $event->event_date }}</td>
                <td>{{ $event->venue->name }}</td>
                @if(Auth::user()->isAdmin())
                    <td>
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $events->appends(request()->query())->links() }}
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this item?');
}
</script>
@endsection
