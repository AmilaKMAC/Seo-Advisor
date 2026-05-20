@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-6">SEO Report</h2>

<!-- SCORE CARDS -->
<div class="grid grid-cols-4 gap-4 mb-6">

    @include('components.score-card', [
        'title' => 'On Page',
        'score' => $report->on_page_score
    ])

    @include('components.score-card', [
        'title' => 'Technical',
        'score' => $report->technical_score
    ])

    @include('components.score-card', [
        'title' => 'Off Page',
        'score' => $report->off_page_score
    ])

    @include('components.score-card', [
        'title' => 'Overall',
        'score' => $report->overall_score
    ])

</div>

<!-- ON-PAGE SEO -->
<div class="bg-white p-6 rounded shadow mb-4">
    <h3 class="font-semibold mb-3">On-Page SEO Issues</h3>

    @if($report->issues->count())
        @foreach($report->issues as $issue)
            <p class="text-red-500">❌ {{ $issue->issue }}</p>
        @endforeach
    @else
        <p class="text-green-600">✅ No major issues</p>
    @endif
</div>

<!-- TECHNICAL SEO -->
<div class="bg-white p-6 rounded shadow mb-4">
    <h3 class="font-semibold mb-3">Technical SEO</h3>

    <p>
        Performance:
        {{ $report->page_speed_data['performance'] ?? 'N/A' }}
    </p>
</div>

<!-- DOMAIN SUGGESTIONS -->
<div class="bg-white p-6 rounded shadow mb-4">
    <h3 class="font-semibold mb-3">Domain Suggestions</h3>

    @if($report->domains->count())
        @foreach($report->domains as $domain)
            <div class="border p-3 rounded mt-2">
                <strong>{{ $domain->domain }}</strong>
                <p>Score: {{ $domain->score }}</p>
            </div>
        @endforeach
    @else
        <p>No domain suggestions yet</p>
    @endif
</div>

<!-- HOSTING RECOMMENDATION -->
<div class="bg-white p-6 rounded shadow mb-4">
    <h3 class="font-semibold mb-3">Hosting Recommendation</h3>

    @if($report->hosting->count())
        @foreach($report->hosting as $host)
            <div class="border p-3 rounded mt-2">
                <strong>{{ $host->platform }}</strong>
                <p>{{ $host->reason }}</p>
            </div>
        @endforeach
    @else
        <p>No hosting suggestions yet</p>
    @endif
</div>

<!-- CHATBOT -->
<div class="bg-white p-6 rounded shadow">
    <h3 class="font-semibold mb-3">AI Assistant</h3>

    <form action="{{ route('chat') }}" method="POST">
        @csrf

        <input type="hidden" name="report_id" value="{{ $report->id }}">

        <input 
            type="text" 
            name="message"
            placeholder="Ask something..."
            class="w-full border p-2 rounded mb-2"
        >

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Send
        </button>
    </form>
</div>

@endsection