@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="row justify-content-center g-4">
    <div class="col-lg-5">
        <div class="card ls-card">
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="text-teal fw-bold">Welcome back</div>
                    <h1 class="h3 mb-1">Sign in to LearnSphere</h1>
                    <p class="text-muted small mb-0">Use a demo account below or your own credentials.</p>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="loginPassword" class="form-control form-control-lg" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" data-bs-toggle="tooltip" title="Show password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-ls-primary btn-lg w-100">Sign in</button>
                </form>
                <p class="small mt-3 mb-0">No account? <a href="{{ route('register') }}">Register as learner</a></p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card ls-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Demo accounts</span>
                <span class="badge bg-mint text-teal">password for all</span>
            </div>
            <div class="card-body">
                <p class="small text-muted">Click a row to auto-fill the login form, then press Sign in.</p>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Role</th><th>Email</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['Super Admin', 'admin@learnsphere.test', 'Platform control'],
                                ['Instructor', 'instructor@learnsphere.test', 'Build courses'],
                                ['Learner', 'learner@learnsphere.test', 'Take courses'],
                                ['Content Manager', 'content@learnsphere.test', 'Publish content'],
                                ['Support', 'support@learnsphere.test', 'Help learners'],
                            ] as [$role, $email, $hint])
                                <tr class="demo-account" data-demo-email="{{ $email }}" data-bs-toggle="tooltip" title="Click to fill {{ $email }}">
                                    <td>
                                        <strong>{{ $role }}</strong>
                                        <div class="small text-muted">{{ $hint }}</div>
                                    </td>
                                    <td><code>{{ $email }}</code></td>
                                    <td class="text-end"><span class="btn btn-sm btn-outline-secondary">Use</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('togglePassword')?.addEventListener('click', function () {
    const input = document.getElementById('loginPassword');
    const icon = this.querySelector('i');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>
@endpush
