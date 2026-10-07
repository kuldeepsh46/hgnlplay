@extends('common.layout')
@section('title', 'Edit User')
@section('main')
<style>

body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
    min-height: 100vh;
}
.card {
    background: var(--card);
    border: 1px solid #e5f2e6;
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: 0 0 20px #00000050;
    margin-bottom: 24px;
}



.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 6px;
    color: var(--muted);
}

input,
select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #e5f2e6;
    background: #ffffff;
    color: #203c30;
}

.btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-save {
    background: var(--accent);
    color: #000;
}

.btn-back {
    background: #e5f2e6;
    color: #fff;
    margin-left: 8px;
}

.alert {
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert-success {
    background: #23845b;
    color: #000;
}

.alert-error {
    background: #ff4a4a;
    color: #203c30;
}
</style>

<h1>Edit User</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>User Name</label>
            <input type="text" name="username" value="{{ $user->username ?? $user->name }}" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>
        </div>

        <div class="form-group">
            <label>Mobile</label>
            <input type="text" name="mobile" value="{{ $user->mobile ?? '' }}">
        </div>



        <div class="form-group">
            <label>Wallet Balance (₹)</label>
            <input type="number" name="balance" value="{{ $wallet->balance ?? 0 }}" step="0.01">
        </div>

        <div class="form-group">
            <label for="repurchase_balance">Repurchase Wallet Balance (₹)</label>
            <input type="number" id="repurchase_balance" value="{{ $wallet->repurchase_balance ?? 0 }}" readonly
                aria-describedby="repurchase_balance_help">
            <small id="repurchase_balance_help">Read-only. Filled automatically with 10% of every earning.</small>
        </div>

        <button class="btn btn-save" type="submit">💾 Save Changes</button>
        <a href="{{ route('admin.users') }}" class="btn btn-back">← Back</a>
    </form>
</div>
@endsection