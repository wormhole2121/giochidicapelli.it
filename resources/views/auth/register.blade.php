<x-layout>
    @guest
        <div class="container">
            
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="register-container">
                        <div class="register-inner-container">
                            <h2 class="register-title text-center mb-5 mt-1">Registrati qui</h2>
                            <form method="POST" action="{{ route('register') }}" class="login-form inputs-grid">
                                @csrf

                                <div class="input-wrapper form-group">
                                    <input class="login-input form-control" type="text" name="name" placeholder="Nome" required>
                                    @error('name')
                                        <p class="login-error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="input-wrapper form-group">
                                    <input class="login-input form-control" type="text" name="surname" placeholder="Cognome" required>
                                    @error('surname')
                                        <p class="login-error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="input-wrapper form-group">
                                    <input class="login-input form-control" type="email" name="email" placeholder="Email" required>
                                    @error('email')
                                        <p class="login-error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="input-wrapper form-group">
                                    <input class="login-input form-control" type="tel" name="phone" placeholder="Numero di telefono" required>
                                    @error('phone')
                                        <p class="login-error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="input-wrapper form-group">
                                    <input class="login-input form-control" type="password" name="password" placeholder="Password" required>
                                    @error('password')
                                        <p class="login-error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="input-wrapper form-group">
                                    <input class="login-input form-control" type="password" name="password_confirmation" placeholder="Conferma Password" required>
                                    @error('password_confirmation')
                                        <p class="login-error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button class="login-submit-btn btn btn-primary" type="submit">Registrati</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endguest
</x-layout>
