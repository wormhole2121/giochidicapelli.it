<x-layout>
    <a href="{{ route('calendario') }}">
        <i class="fa-solid fa-arrow-left fa-2x ms-2 mt-2" style="color: black;"></i>
    </a>
    <h2 class="text-center mb-4 custom-title" style="font-size: 35px; color:black; margin-right:30px">Le mie Prenotazioni</h2>
    <div class="container">
        <!-- Sezione per i messaggi di errore e successo -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
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
                        setTimeout(function() {
                            window.location.href = '/calendario';
                        }, 3000);
                    </script>
                @else
                    <!-- Ordinare le prenotazioni per data crescente -->
                    @php
                        $sortedBookings = $userBookings->sortBy('date');
                    @endphp

                    @foreach ($sortedBookings->groupBy('date') as $date => $bookings)
                        <h3 class="custom-title-2" style="color: black;">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd D MMMM YYYY') }}</h3>
                        <ul class="list-group">
                            @foreach ($bookings as $booking)
                                <li class="list-group-item" style="margin-bottom:20px; border-radius:10px">
                                    <strong>Orario:</strong>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    <br>
                                    <strong>Nome:</strong> {{ $booking->name }}
                                    <br>
                                    <strong>Numero di Telefono:</strong> {{ $booking->phone }}
                                    <br>
                                    <strong>Tipologie di Taglio:</strong>
                                    @if (is_array(json_decode($booking->haircut_types, true)))
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
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-layout>
