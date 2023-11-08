<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Swiper demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
  <!-- Link Swiper's CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Demo styles -->
  <style>
    /* Imposta lo sfondo nero per il container principale di Swiper */
    .swiper {
      width: 100%;
      padding-top: 50px;
      padding-bottom: 50px;
      background: linear-gradient(184deg, rgba(25,22,12,0.8741667076987045) 46%, rgba(163,164,164,1) 83%);
      border-radius: 10px;
      margin-top: 30px;
      margin-bottom: 10px;
      padding-right: 5px;
      padding-left: 5px;
      
    }
  
    .swiper-slide {
      background-position: center;
      background-size: cover;
      width: 300px; /* Larghezza delle slide */
      height: 300px; /* Altezza delle slide */
      margin-left: 5px; /* Spaziatura tra le slide - sinistra */
      margin-right: 5px; /* Spaziatura tra le slide - destra */
    }
  
    .swiper-slide img {
      display: block;
      width: 100%; /* Larghezza dell'immagine al 100% della slide */
      height: auto; /* Altezza automatica per mantenere le proporzioni */
      border-radius: 8px; /* Angoli arrotondati per l'immagine */
    }
  </style>
  
</head>

<body>

    <div class="container">
        <div class="row">
        <h2 class="text-center titolo">Galleria</h2>
        </div>
    </div>

  <!-- Swiper -->
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <img src="media/immagine1.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine2.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine3.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine4.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine5.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine6.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine7.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine8.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine9.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine10.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine11.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine12.jpg" />
      </div>
      <div class="swiper-slide">
        <img src="media/immagine13.jpg" />
      </div>
    </div>
    <div class="swiper-pagination"></div>
  </div>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
    var swiper = new Swiper(".mySwiper", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      loop: true, // Continua a scorrere all'infinito
      autoplay: {
        delay: 2500, // Tempo tra le transizioni automatiche
        disableOnInteraction: false, // Continua l'autoplay anche dopo l'interazione dell'utente
      },
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: true,
      },
      pagination: {
        el: ".swiper-pagination",
      },
    });
  </script>
  
</body>

</html>