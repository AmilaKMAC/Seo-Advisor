<div class="bg-white p-6 rounded shadow mb-4">

    <h3>Hosting Recommendation</h3>

    @foreach($report->hosting as $host)

        <div class="border p-3 rounded mt-2">
            <strong>{{ $host->platform }}</strong>
            <p>{{ $host->reason }}</p>
        </div>

    @endforeach

</div>
