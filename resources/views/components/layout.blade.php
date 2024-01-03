<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- meta json -->
    <meta name="theme-color" content="#808080">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- json -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" sizes="48x48" href="/media/icona-app-48x48.png">
    <link rel="apple-touch-icon" sizes="96x96" href="/media/icona-app-96x96.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/media/icona-app-144x144.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/media/icona-app-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/media/icona-app-512x512.png">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"rel="stylesheet" />
    {{-- aos --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    {{-- google font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&family=Lobster&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Roboto+Condensed:ital,wght@1,100&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fjalla+One&family=Lobster&family=Roboto+Condensed:ital,wght@1,100&display=swap"
        rel="stylesheet">


    <link rel="icon" type="image/png" sizes="190x190" href="/media/icona-app.png">
    <!-- <link rel="icon" type="image/png" sizes="512x512" href="/path/to/icon-512x512.png"> -->



    <!-- tagli -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />


    @vite('resources/css/app.css')
    <title>Giochi di capelli</title>
</head>

<body>

    <x-navbar></x-navbar>
    <header class="container-image">

    </header>

    {{-- <div class="intro">
        <i class="fa-solid fa-arrow-up" style="color: #775e18;"></i>
        <h1>Per visualizzare i contenuti Accedi o egistrati</h1>
    </div> --}}


    {{ $slot }}


    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    {{-- aos js --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#haircut_types').select2();
        });
    </script>



    @vite('resources/js/app.js')
</body>

</html>
