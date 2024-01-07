<h1>Conferma Prenotazione</h1>
<p>Gentile {{ $booking->user->name }},</p>
<p>La tua prenotazione è stata confermata per il giorno {{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }} alle ore {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}.</p>
<p>Grazie per aver scelto il nostro servizio.</p>


