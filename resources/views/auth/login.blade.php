<x-layout>
    @guest
        <div class="login-container">
            <div class="login-inner-container">
                <h2 class="login-title text-center mb-5 mt-1">Accedi qui</h2>
                <div class="login-row justify-content-center">
                    <div class="login-main-box">
                        <form method="POST" action="{{ route('login') }}" class="login-form">
                            @csrf
                            <input class="login-input email-input" type="email" name="email" placeholder="Email" required>
                            @error('email')
                                <p class="login-error-text email-error">{{ $message }}</p>
                            @enderror

                            <input class="login-input password-input" type="password" name="password" placeholder="Password"
                                required>
                            @error('password')
                                <p class="login-error-text password-error">{{ $message }}</p>
                            @enderror

                            <div class="form-group">
                                <input type="checkbox" name="remember" id="remember">
                                <label class="text-white" for="remember">Rimani collegato</label>
                            </div>

                            <a href="{{ route('password.request') }}" class="forgot-password-link">Password dimenticata?</a>

                            <button class="login-submit-btn" type="submit">Accedi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endguest
</x-layout>
