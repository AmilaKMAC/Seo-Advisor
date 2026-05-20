<div class="bg-white p-6 rounded shadow mb-4">

    <h3 class="font-semibold mb-3">On-Page SEO Issues</h3>

    @foreach($report->issues as $issue)

        <p class="text-red-500">
            ❌ {{ $issue->issue }}
        </p>

    @endforeach

</div>