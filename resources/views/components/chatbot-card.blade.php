<div class="bg-white p-6 rounded shadow">

    <h3>AI Chatbot</h3>

    <div class="h-40 overflow-y-auto border p-3 mb-3">

        @foreach($report->chats as $chat)

            <p>
                <strong>{{ $chat->role }}</strong>: 
                {{ $chat->message }}
            </p>

        @endforeach

    </div>

     }}" method="POST">
        @csrf

        <input type="hidden" name="report_id" value="{{ $report->id }}">

        <input 
            type="text" 
            name="message"
            class="border p-2 w-full"
            placeholder="Ask AI..."
        >

    </form>

</div>