<!-- resources/views/webapp/error.blade.php -->
@extends('webapp.layout')
@section('content')
    <div class="flex flex-col items-center justify-center h-screen text-center">
        <div class="text-red-500 text-5xl mb-4">⚠️</div>
        <p class="text-lg">{{ $message }}</p>
    </div>
@endsection
