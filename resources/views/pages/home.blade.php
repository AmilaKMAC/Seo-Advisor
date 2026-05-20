@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-4">Analyze Website</h2>

<form action="{{ route('analyze') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
    @csrf

    <input 
        type="url" 
        name="website_url"
        placeholder="https://example.com"
        class="w-full border p-2 rounded"
        required
    >

    <input 
        type="text" 
        name="project_name"
        placeholder="Project name"
        class="w-full border p-2 rounded"
    >

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
        Analyze
    </button>

</form>

@endsection
``