<x-layout>
    <div class="my-bookings">

        {{-- HEADER PREMIUM --}}
        <div class="page-head-premium">
            <a href="{{ route('calendario') }}" class="back-link-premium" aria-label="Torna al calendario">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="page-head-text-premium">
                <h2 class="page-title-premium ">Le mie Prenotazioni</h2>
            </div>


        </div>

        <div class="container">

            @if (session('error'))
                <div class="alert alert-danger bookings-alert">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success bookings-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-8 offset-md-2">

                    @if ($userBookings->isEmpty())
                        <div class="empty-card-premium">
                            <div class="empty-ico-premium">
                                <i class="fa-regular fa-calendar-xmark"></i>
                            </div>
                            <h3>Nessuna prenotazione</h3>
                            <p>Non hai ancora prenotazioni. Ti rimando al calendario tra 3 secondi.</p>
                            <a class="btn btn-dark rounded-pill px-4" href="{{ route('calendario') }}">
                                Vai al calendario
                            </a>
                        </div>

                        <script>
                            setTimeout(function() {
                                window.location.href = '/calendario';
                            }, 3000);
                        </script>
                    @else
                        @php
                            $sortedBookings = $userBookings->sortBy('date');
                        @endphp

                        @foreach ($sortedBookings->groupBy('date') as $date => $bookings)

                            {{-- GIORNO PREMIUM SENZA NUMERI --}}
                            <div class="date-premium">
                                <div class="date-pill">
                                    <span class="date-pill-ico">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <span class="date-pill-text">
                                        {{ \Carbon\Carbon::parse($date)->isoFormat('dddd D MMMM YYYY') }}
                                    </span>
                                </div>
                            </div>

                            <ul class="list-group bookings-list">
                                @foreach ($bookings as $booking)
                                    <li class="list-group-item booking-item-premium" style="margin-bottom:20px; border-radius:10px">

                                        <div class="booking-top-premium">
                                            <div class="time-pill-premium">
                                                <i class="fa-regular fa-clock"></i>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="booking-body-premium">

                                            <div class="booking-line-premium">
                                                <span class="line-ico"><i class="fa-regular fa-user"></i></span>
                                                <div>
                                                    <div class="line-label">Nome</div>
                                                    <div class="line-value">{{ $booking->name }}</div>
                                                </div>
                                            </div>

                                            <div class="booking-line-premium">
                                                <span class="line-ico"><i class="fa-solid fa-phone"></i></span>
                                                <div>
                                                    <div class="line-label">Telefono</div>
                                                    <div class="line-value">{{ $booking->phone }}</div>
                                                </div>
                                            </div>

                                            <div class="booking-line-premium">
                                                <span class="line-ico"><i class="fa-solid fa-scissors"></i></span>
                                                <div>
                                                    <div class="line-label">Tipologie di taglio</div>
                                                    <div class="line-value">
                                                        @if (is_array(json_decode($booking->haircut_types, true)))
                                                            {{ implode(', ', json_decode($booking->haircut_types, true)) }}
                                                        @else
                                                            {{ $booking->haircut_types }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <form action="{{ route('elimina', ['id' => $booking->id]) }}" method="POST" class="delete-form-premium">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                            <button type="submit" class="btn-delete-premium">
                                                <i class="fa-regular fa-trash-can"></i>
                                                Elimina Prenotazione
                                            </button>
                                        </form>

                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>

    </div>
</x-layout>
