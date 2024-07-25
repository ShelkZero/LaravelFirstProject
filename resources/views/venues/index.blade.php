@extends('layouts.app')

@section('content')
<div class="container">
    @if(Auth::user()->isAdmin())
        <a href="{{ route('venues.create') }}" class="btn btn-primary mb-3">Create Venue</a>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('venues.index', ['sort_by' => 'id', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        ID
                    </a>
                </th>
                <th>
                    <a href="{{ route('venues.index', ['sort_by' => 'name', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Name
                    </a>
                </th>
                @if(Auth::user()->isAdmin()) <!-- Проверка, является ли текущий пользователь администратором -->
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($venues as $venue)
            <tr>
                <td>{{ $venue->id }}</td>
                <td>{{ $venue->name }}</td>
                @if(Auth::user()->isAdmin()) 
                    <td>
                        <a href="{{ route('venues.edit', $venue) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('venues.destroy', $venue) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
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
    {{ $venues->appends(request()->query())->links() }}
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this item?');
}
</script>
@endsection
