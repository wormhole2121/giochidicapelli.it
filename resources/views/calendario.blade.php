<x-layout>
    @auth
        <div class="container booking-container">
            <h2 class="text-center booking-header">Calendario delle Prenotazioni</h2>

            <!-- Sezione per i messaggi di errore e successo -->
            <div class="feedback-section">
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


            </div>

            <div class="row booking-content">
                <!-- Form per selezionare la data -->
                <div class="col-md-6 col-sm-12 datepicker-section">
                    <div class="calendar-wrapper">
                        <div class="header month-selector">
                            <button id="prevMonth" class="btn month-prev active">&lt;</button>
                            <h2 id="currentMonth" class="text-center month-display"></h2>
                            <button id="nextMonth" class="btn month-next active">&gt;</button>
                        </div>
                        <div class="days weekdays-header">
                            <div class="day">Dom</div>
                            <div class="day">Lun</div>
                            <div class="day">Mar</div>
                            <div class="day">Mer</div>
                            <div class="day">Gio</div>
                            <div class="day">Ven</div>
                            <div class="day">Sab</div>
                        </div>
                        <div class="dates days-display" id="dates">
                            @foreach ($availableDates as $date)
                                <div class="date">
                                    <span data-booking-date="{{ $date }}"
                                        class="day">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <form action="{{ route('calendario') }}" class="date-selection-form" method="GET"
                        style="display: none;">
                        <div class="form-group date-picker-group">
                            <label for="date">Seleziona una data:</label>
                            <select name="date" id="date" class="form-control date-dropdown">
                                <option value="" disabled selected>Seleziona una data</option>
                                @foreach ($availableDates as $date)
                                    <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                        {{ $date }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-show-bookings">Mostra prenotazioni</button>
                    </form>

                </div>

                <!-- Modulo di Prenotazione -->
                <div class="col-md-6 col-sm-12 reservation-section">
                    <div class="card reservation-card">

                        <div class="card-body reservation-body">
                            <h3 class="card-header reservation-header text-center t-white mb-2">Prenota un Appuntamento</h3>
                            <form action="{{ route('prenota') }}" class="reservation-form" method="POST">
                                @csrf
                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                <div class="form-group">
                                    <label for="name" class="t-white">Nome e Cognome:</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="t-white">Numero di Telefono:</label>
                                    <input type="text" name="phone" id="phone" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <p class="t-white">*Ogni taglio comprende lo shampoo*</p>
                                    <label for="haircut_types" class="t-white">Tipologia di Tagli:</label>
                                    <select multiple name="haircut_types[]" id="haircut_types" class="form-control" multiple
                                        required>
                                        <option value="Taglio">Taglio</option>
                                        <option value="Taglio con modellatura barba">Taglio con modellatura barba</option>
                                        <option value="Taglio Razor fade(Sfumatura)">Taglio Razor fade (sfumatura)</option>
                                        <option value="Taglio Children">Taglio Children (fino a 10 anni)</option>
                                        <option value="Modellatura barba">Modellatura barba</option>
                                    </select>
                                </div>


                                <div class="form-group available-time-buttons t-white">
                                    <label>Seleziona un orario:</label>
                                    <div class="time-buttons-wrapper">
                                        @foreach ($availableTimes as $index => $hour)
                                            <input type="radio" id="time-{{ $index }}" name="start_time"
                                                value="{{ $hour }}" class="time-radio"
                                                {{ old('start_time') == $hour ? 'checked' : '' }}>
                                            <label for="time-{{ $index }}"
                                                class="btn btn-outline-primary time-btn">{{ $hour }}</label>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn confirm-reservation-btn">Prenota</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Visualizzazione delle prenotazioni -->
            @if ($selectedDate)
                <ul class="list-group bookings-list my-3">
                    @foreach ($bookings as $booking)
                        @if (Auth::check() && (Auth::user()->is_admin || $booking->user_id == Auth::id()))
                            <li class="list-group-item booking-item" style="margin-bottom:20px; border-radius:10px">
                                <strong>Data:</strong>
                                {{ \Carbon\Carbon::parse($booking->date)->isoFormat('dddd, D MMMM YYYY') }}
                                <br>
                                <strong>Orario:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                <br>
                                <strong>Nome Utente:</strong> {{ $booking->user->name }} {{ $booking->user->surname }}
                                <br>
                                <strong>Numero di Telefono:</strong> {{ $booking->phone }}
                                <br>
                                <strong>Tipologie di Taglio:</strong>
                                {{ is_array(json_decode($booking->haircut_types, true)) ? implode(', ', json_decode($booking->haircut_types, true)) : $booking->haircut_types }}

                                {{-- Logica per il pulsante di eliminazione, se necessario --}}
                                <form action="{{ route('elimina', ['id' => $booking->id]) }}" class="delete-booking-form"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                    <button type="submit" class="btn delete-booking-btn mt-2">Elimina</button>
                                    <a href="{{ route('le-mie-prenotazioni', ['id' => $booking->id]) }}"
                                        class="btn btn-info mt-2">Mostra dettagli</a>
                                </form>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif


        </div>

    @endauth

</x-layout>
