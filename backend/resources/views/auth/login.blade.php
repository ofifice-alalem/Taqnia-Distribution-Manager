@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</h4>
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
                        <i class="fas fa-sign-in-alt"></i> دخول
                    </button>
                </form>
                
                <hr>
                
                <div class="alert alert-info">
                    <strong>حسابات تجريبية:</strong><br>
                    <small>
                        <strong>مدير:</strong> admin / password<br>
                        <strong>أمين مخزن:</strong> warehouse / password<br>
                        <strong>مسوق:</strong> salesman / password
                    </small>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('register') }}" class="btn btn-link">
                        ليس لديك حساب؟ سجل الآن
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection