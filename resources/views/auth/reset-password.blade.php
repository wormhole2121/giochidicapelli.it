<x-layout>
    <div class="reset-password-container my-3">
        <div class="reset-password-inner-container col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
            <h2 class="text-center mb-4">Reimposta Password</h2>
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('password.update') }}" method="post" class="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label for="email" class="reset-password-label">Email:</label>
                    <input type="email" name="email" id="email" class="reset-password-input form-control" required>
                </div>

                <div class="form-group">
                    <label for="password" class="reset-password-label">Nuova Password:</label>
                    <input type="password" name="password" id="password" class="reset-password-input form-control" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="reset-password-label">Conferma Password:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="reset-password-input form-control" required>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="reset-password-submit-btn btn w-100">Reimposta Password</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
