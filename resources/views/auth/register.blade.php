<x-layout>
    @guest
    <h2 class="register-title text-center">Registrati qui</h2>
    <div class="login-container">
        
        <div class="login-inner-container">
            <form method="POST" action="{{ route('register') }}" class="login-form">
                @csrf
                <input class="login-input" type="text" name="name" placeholder="Nome" required>
                @error('name')
                <p class="login-error-text">{{ $message }}</p>
                @enderror

                <input class="login-input" type="text" name="surname" placeholder="Cognome" required>
                @error('surname')
                <p class="login-error-text">{{ $message }}</p>
                @enderror
                
                <input class="login-input" type="email" name="email" placeholder="Email" required>
                @error('email')
                <p class="login-error-text">{{ $message }}</p>
                @enderror
                
                <input class="login-input" type="text" name="phone" placeholder="Numero di telefono" required>
                @error('phone')
                <p class="login-error-text">{{ $message }}</p>
                @enderror
                
                <input class="login-input" type="password" name="password" placeholder="Password" required>
                @error('password')
                <p class="login-error-text">{{ $message }}</p>
                @enderror
                
                <input class="login-input" type="password" name="password_confirmation" placeholder="Conferma Password" required>
                @error('password_confirmation')
                <p class="login-error-text">{{ $message }}</p>
                @enderror
                
                <button class="login-submit-btn" type="submit">Registrati</button>
            </form>
        </div>
    </div>
    @endguest
</x-layout>
