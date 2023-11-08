<x-layout>
    <a href="{{route('calendario')}}">
        <i class="fa-solid fa-arrow-left fa-2x ms-2 mt-2" style="color: black;"></i>
    </a>
    <h2 class="text-center mb-4" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">Le mie Prenotazioni</h2>
    <div class="container">
        <!-- Sezione per i messaggi di errore e successo -->
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="row">
            <div class="col-md-8 offset-md-2">
                @if ($userBookings->isEmpty())
                    <div class="alert alert-info">
                        Non hai ancora effettuato nessuna prenotazione.
                    </div>
                    <script>
                        // Attendi 3 secondi e reindirizza all'url del calendario
                        setTimeout(function() {
                            window.location.href = '/calendario'; // Sostituisci con l'URL del calendario
                        }, 3000); // 3000 millisecondi = 3 secondi
                    </script>
                @else
                    <ul class="list-group">
                        @foreach ($userBookings as $booking)
                            <li class="list-group-item" style="margin-bottom:20px; border-radius:10px">
                                <strong>Data:</strong> {{ Carbon\Carbon::parse($booking->date)->isoFormat('dddd D MMMM YYYY') }}
                                <br>
                                <strong>Orario:</strong> {{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                <br>
                                <strong>Nome:</strong> {{ $booking->name }}
                                <br>
                                <strong>Numero di Telefono:</strong> {{ $booking->phone }}
                                <br>
                                <strong>Tipologie di Taglio:</strong>
                                @if(is_array(json_decode($booking->haircut_types, true)))
                                {{ implode(', ', json_decode($booking->haircut_types, true)) }}
                                @else
                                {{ $booking->haircut_types }}
                                @endif
                                <form action="{{ route('elimina', ['id' => $booking->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                    <button type="submit" class="btn btn-danger mt-2">Elimina Prenotazione</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-layout>
