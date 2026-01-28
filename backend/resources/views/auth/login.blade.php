@extends('layouts.auth')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="bi bi-box-arrow-in-right"></i> تسجيل الدخول</h4>
    </div>
    
    <div class="card-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">اسم المستخدم</label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">تذكرني</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> دخول
            </button>
        </form>
        
        <div class="text-center">
            <a href="{{ route('register') }}" class="btn btn-link">
                ليس لديك حساب؟ سجل الآن
            </a>
        </div>
    </div>
</div>
@endsection