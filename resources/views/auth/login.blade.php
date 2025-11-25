@extends('layouts.auth')

@section('title', 'Login - ERP System')

@section('content')
<div class="auth-header">
    <h1>Welcome Back</h1>
    <p>Sign in to your ERP account</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email Address</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            required 
            autofocus
            placeholder="Enter your email"
        >
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            required
            placeholder="Enter your password"
        >
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div style="text-align: right; margin-bottom: 20px;">
        <a href="{{ route('password.request') }}" style="color: #667eea; text-decoration: none; font-size: 14px;">Forgot Password?</a>
    </div>

    <button type="submit" class="btn">Login</button>
</form>
@endsection

