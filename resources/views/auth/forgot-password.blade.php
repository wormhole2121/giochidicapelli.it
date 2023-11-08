<x-layout>
    <div class="reset-password-container">
        <div class="reset-password-inner-container col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
            <h2 class="text-center mb-4">Richiedi Link di Reimpostazione Password</h2>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form action="{{ route('password.email') }}" method="post" class="reset-password-form">
                @csrf
                <div class="form-group my-2">
                    <label for="email" class="reset-password-label">Email:</label>
                    <input type="email" name="email" id="email" class="reset-password-input form-control">
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="reset-password-submit-btn btn w-100">Invia il Link</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
