<nav id="navbar" class="navbar navbar-expand-lg bg-body-tertiary sticky-top fs-5">
    <div class="container-fluid d-flex justify-content-between align-items-center" id="colorNav">
  
      <!-- Titolo mobile -->
      <div class="d-lg-none d-flex flex-grow-1 justify-content-center title-mobile text-center fw-bold custom-title">
        Giochi di capelli
      </div>
  
      <!-- Toggler SOLO mobile -->
      <button class="navbar-toggler bg-white d-lg-none" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
        aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
  
      <!-- ===== DESKTOP MENU (UGUALE AL TUO, NON TOCCATO) ===== -->
      <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex">
        <ul class="navbar-nav flex-grow-1 pe-3 justify-content-center">
  
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">Home</a>
          </li>
  
          @auth
            <li class="nav-item">
              <a class="nav-link" href="{{ route('calendario') }}">Prenota</a>
            </li>
  
            <li class="nav-item">
              <a class="nav-link" href="{{ route('le-mie-prenotazioni') }}">Le mie prenotazioni</a>
            </li>
  
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                Benvenuto {{ Auth::user()->name }}
              </a>
              <ul class="dropdown-menu">
                <li>
                  <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item">Logout</button>
                  </form>
                </li>
              </ul>
            </li>
          @else
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                Benvenuto Ospite
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('register') }}">Registrati</a></li>
                <li><a class="dropdown-item" href="{{ route('login') }}">Accedi</a></li>
              </ul>
            </li>
          @endauth
  
        </ul>
      </div>
  
      <!-- ===== OFFCANVAS SOLO MOBILE (MODERNO) ===== -->
      <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
  
        <!-- HEADER OFFCANVAS -->
        <div class="offcanvas-header d-flex align-items-center gap-3">
  
          <div class="user-avatar-modern">
            <span class="user-initial">
              {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'O' }}
            </span>
          </div>
  
          @auth
            <span class="user-welcome-text">Benvenuto {{ Auth::user()->name }}</span>
          @else
            <span class="user-welcome-text">Benvenuto Ospite</span>
          @endauth
  
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
  
        <!-- BODY OFFCANVAS -->
        <div class="offcanvas-body">
          <ul class="navbar-nav flex-grow-1 pe-0 justify-content-start offcanvas-menu">
  
            <li class="nav-item">
              <a class="nav-link off-item" href="{{ route('home') }}">
                <span class="off-ico"><i class="fa-solid fa-house"></i></span>
                <span class="off-text">Home</span>
                <span class="off-arrow"><i class="fa-solid fa-chevron-right"></i></span>
              </a>
            </li>
  
            @auth
              <li class="nav-item">
                <a class="nav-link off-item" href="{{ route('calendario') }}">
                  <span class="off-ico"><i class="fa-solid fa-calendar-days"></i></span>
                  <span class="off-text">Prenota</span>
                  <span class="off-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </a>
              </li>
  
              <li class="nav-item">
                <a class="nav-link off-item" href="{{ route('le-mie-prenotazioni') }}">
                  <span class="off-ico"><i class="fa-solid fa-bookmark"></i></span>
                  <span class="off-text">Le mie prenotazioni</span>
                  <span class="off-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </a>
              </li>
  
              <li class="nav-item off-divider"></li>
  
              <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="off-logout">
                    <span class="off-ico"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span class="off-text">Logout</span>
                  </button>
                </form>
              </li>
            @else
              <li class="nav-item off-divider"></li>
  
              <li class="nav-item">
                <a class="nav-link off-item" href="{{ route('register') }}">
                  <span class="off-ico"><i class="fa-solid fa-user-plus"></i></span>
                  <span class="off-text">Registrati</span>
                  <span class="off-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </a>
              </li>
  
              <li class="nav-item">
                <a class="nav-link off-item" href="{{ route('login') }}">
                  <span class="off-ico"><i class="fa-solid fa-right-to-bracket"></i></span>
                  <span class="off-text">Accedi</span>
                  <span class="off-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </a>
              </li>
            @endauth
  
          </ul>
        </div>
      </div>
  
    </div>
  </nav>
  