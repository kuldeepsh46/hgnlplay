@extends('common.layout')
@section('title', 'Team Structure')
@section('main')

    <style>
    .tree-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 20px 20px;
    }.card {
    background-color: #fff;
    border-radius: 10px;
}
    </style>

        <div class="header">
            <h1>Team StructureS</h1>
            <div class="user-info">
                👤 {{ $user->username ?? $user->name }}
            </div>
        </div>

        <div class="card">
            <div class="tree-scroll">
                <div class="tree-container">
                    @include('partials.tree-node', ['node' => $tree])
                </div>
            </div>
        </div>





    <style>
    /* ===== Password Modal ===== */
    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(11, 14, 18, 0.9);
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        width: 90%;
        max-width: 400px;
        color: var(--text);
        position: relative;
        box-shadow: 0 0 30px #00000080;
    }

    .modal-content h2 {
        margin-top: 0;
        color: var(--accent);
        text-align: center;
    }

    .modal-content .close {
        position: absolute;
        top: 10px;
        right: 16px;
        font-size: 22px;
        cursor: pointer;
        color: var(--muted);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--muted);
        font-size: 14px;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #2a3442;
        background: #0f1620;
        color: #fff;
    }
    </style>
    <!-- Password Change Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Change Password</h2>

            <form id="passwordForm" method="POST" action="{{ route('changep.update') }}">
                @csrf
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                        minlength="6">
                </div>
                <button type="submit" class="btn btn-copy" style="width:100%;">Update Password</button>
            </form>
            @if (session('success'))
            <p style="color:#a7ff1e; text-align:center;">{{ session('success') }}</p>
            @endif
            @if (session('error'))
            <p style="color:#ff5555; text-align:center;">{{ session('error') }}</p>
            @endif
        </div>


    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    
@endsection