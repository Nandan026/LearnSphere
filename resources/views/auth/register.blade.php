@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card ls-card">
            <div class="card-body p-4">
                <div class="text-teal fw-bold mb-1">Join LearnSphere</div>
                <h1 class="h3 mb-2">Create learner account</h1>
                <p class="text-muted small">New accounts start with the Learner role so you can enroll immediately.</p>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" required>
                    </div>
                    <button type="submit" class="btn btn-ls-primary btn-lg w-100">Create account</button>
                </form>
                <p class="small mt-3 mb-0">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
