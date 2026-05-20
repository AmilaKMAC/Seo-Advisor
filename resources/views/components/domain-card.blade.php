<div class="bg-white p-6 rounded shadow mb-4">

    <h3>Domain Suggestions</h3>

    @foreach($report->domains as $domain)

        <div class="border p-3 rounded mt-2">
            {{ $domain->domain }} ({{ $domain->score }}%)
        </div>

    @endforeach

</div>