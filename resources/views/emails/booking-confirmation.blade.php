<h1>Conferma Prenotazione</h1>
<p>Gentile {{ $booking->user->name }},</p>
<p>La tua prenotazione è stata confermata per il giorno {{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }} alle ore {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}.</p>
<p>Ricordiamo che gli appuntamenti devono essere disdetti almeno 24 ore prima dell'orario previsto per l'appuntamento. Grazie per aver scelto il nostro servizio.</p>


