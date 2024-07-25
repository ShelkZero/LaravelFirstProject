@extends('layouts.app')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                @if (Auth::check() && Auth::user()->isAdmin())
                    {{ __("You're logged in, hello admin!") }}
                @else
                    {{ __("You're logged in!") }}
                @endif
                </div>
            </div>
        </div>
    </div>
@endsection
