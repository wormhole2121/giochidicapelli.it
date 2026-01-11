<x-layout>
    @auth
        <div class="container booking-container">
            <h2 class="text-center booking-header">Calendario delle Prenotazioni</h2>

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

                <!-- Mostra un messaggio se la data selezionata è completamente prenotata -->
                @if ($isFullyBooked)
                    <div class="alert alert-danger">
                        Non ci sono più disponibilità per la data selezionata: {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}.
                    </div>
                @endif
            </div>

            <div class="row booking-content">
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

                        <div class="dates days-display" id="dates"></div>
                    </div>

                    <form action="{{ route('calendario') }}" class="date-selection-form" method="GET" style="display: none;">
                        <div class="form-group date-picker-group">
                            <label for="date">Seleziona una data:</label>
                            <select name="date" id="date" class="form-control date-dropdown">
                                <option value="" disabled selected>Seleziona una data</option>
                                @foreach ($availableDates as $date)
                                    <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                        {{ $date }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-show-bookings">Mostra prenotazioni</button>
                    </form>
                </div>

                {{-- =========================
                     PRENOTAZIONE (solo stile, logica identica)
                     ========================= --}}
                <div class="col-md-6 col-sm-12 reservation-section">
                    <div class="reservation-card ui-card">
                        <div class="ui-card__header">
                            <h3 class="ui-card__title">Prenota un Appuntamento</h3>
                        </div>

                        <div class="reservation-body ui-card__body">
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
                                    <select multiple name="haircut_types[]" id="haircut_types" class="form-control" required>
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
                                            <input
                                                type="radio"
                                                id="time-{{ $index }}"
                                                name="start_time"
                                                value="{{ $hour }}"
                                                class="time-radio"
                                                {{ old('start_time') == $hour ? 'checked' : '' }}
                                            >
                                            <label for="time-{{ $index }}" class="time-btn">{{ $hour }}</label>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn confirm-reservation-btn">Prenota</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================
                 PRENOTAZIONI MOSTRATE SOTTO (stile come screenshot, NO DATA, più compatte)
                 Logica identica.
                 ========================= --}}
            @if ($selectedDate)
                <div class="bookings-list my-3">
                    @foreach ($bookings as $booking)
                        @if (Auth::check() && (Auth::user()->is_admin || $booking->user_id == Auth::id()))
                            <div class="booking-card ui-card booking-card--compact">

                                {{-- Header: SOLO ORARIO (niente data) --}}
                                <div class="ui-card__header booking-card__header booking-card__header--compact">
                                    <div class="booking-card__time">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </div>
                                </div>

                                <div class="ui-card__body booking-card__body booking-card__body--compact">
                                    <div class="booking-grid booking-grid--compact">

                                        <div class="booking-row booking-row--compact">
                                            <span class="booking-label">Nome</span>
                                            <span class="booking-value">{{ $booking->user->name }} {{ $booking->user->surname }}</span>
                                        </div>

                                        <div class="booking-row booking-row--compact">
                                            <span class="booking-label">Telefono</span>
                                            <span class="booking-value">{{ $booking->phone }}</span>
                                        </div>

                                        <div class="booking-row booking-row--compact">
                                            <span class="booking-label">Tipologie di taglio</span>
                                            <span class="booking-value">
                                                {{ is_array(json_decode($booking->haircut_types, true)) ? implode(', ', json_decode($booking->haircut_types, true)) : $booking->haircut_types }}
                                            </span>
                                        </div>
                                    </div>

                                    <form action="{{ route('elimina', ['id' => $booking->id]) }}" class="delete-booking-form booking-actions booking-actions--compact" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                                        <button type="submit" class="btn delete-booking-btn delete-booking-btn--full">
                                            Elimina Prenotazione
                                        </button>

                                        {{-- Tenuto ma nascosto via CSS per look pulito (se lo vuoi, lo riattiviamo) --}}
                                        <a href="{{ route('le-mie-prenotazioni', ['id' => $booking->id]) }}" class="btn booking-details-btn">
                                            Mostra dettagli
                                        </a>
                                    </form>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>
    @endauth

    <!-- Passa l'array PHP fullyBookedDates al JavaScript come JSON -->
    <script>
        window.isAdmin = @json(Auth::check() && Auth::user()->is_admin);
        window.unavailableDates = @json($unavailableDates);
        window.fullyBookedDates = @json($fullyBookedDates);
    </script>

    <script src="{{ asset('js/calendario.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-layout>
