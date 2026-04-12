@php
    $layout = Auth::check() ? 'common.layout' : 'layouts.app';
    $section = Auth::check() ? 'main' : 'content';
@endphp

@extends($layout)

@section('title', 'Register Member')

@section($section)

<style>
    /* ================= LAYOUT WRAPPER ================= */
    .hgnl-page-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 0;
        box-sizing: border-box;
        @if(!Auth::check())
            background: radial-gradient(circle at 30% -20%, #1a2a1f 0%, transparent 60%), #06090c;
            min-height: 100vh;
        @endif
    }

    /* ================= WIDE GLASS CARD ================= */
    .hgnl-card-wide {
        width: 95%; 
        max-width: 1400px; 
        background-color: #10171f;
        border: 1px solid #1b222b;
        border-radius: 16px;
        padding: 50px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .hgnl-card-wide h2 {
        font-size: 28px;
        margin-top: 0;
        margin-bottom: 40px;
        border-left: 5px solid #a7ff1e;
        padding-left: 20px;
        color: #fff;
    }

    /* ================= 3-COLUMN GRID ================= */
    .hgnl-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px;
    }

    .hgnl-field {
        display: flex;
        flex-direction: column;
    }

    .hgnl-field.full-row {
        grid-column: span 3;
    }

    .hgnl-field label {
        font-size: 12px;
        text-transform: uppercase;
        color: #a0acb3;
        margin-bottom: 10px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* ================= INPUT STYLING ================= */
    .hgnl-input, .hgnl-select {
        width: 100%;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #1b222b;
        background-color: #0b0e12;
        color: #fff;
        font-size: 15px;
        transition: 0.3s;
    }

    .hgnl-input:focus {
        outline: none;
        border-color: #a7ff1e;
        background-color: #0d1218;
    }

    .hgnl-input[readonly] {
        color: #e84e6d;
        font-weight: bold;
        background-color: #12181f;
    }

    /* ================= ACTION BUTTON ================= */
    .hgnl-btn {
        background-color: #a7ff1e;
        color: #000;
        font-weight: 800;
        border: none;
        border-radius: 10px;
        padding: 20px;
        font-size: 16px;
        text-transform: uppercase;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 20px;
    }

    .hgnl-btn:hover {
        background-color: #c1ff5e;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(167, 255, 30, 0.2);
    }

    /* ================= LOGO ================= */
    .hgnl-logo {
        margin-bottom: 40px;
    }
    .hgnl-logo img {
        height: 70px;
    }

    /* ================= SUCCESS TIMER STYLES ================= */
    .success-status-box {
        text-align: center;
        padding: 40px 0;
    }
    .timer-circle {
        font-size: 64px;
        font-weight: 900;
        color: #a7ff1e;
        margin: 25px 0;
        text-shadow: 0 0 20px rgba(167, 255, 30, 0.4);
    }
    .manual-link {
        color: #a7ff1e;
        text-decoration: none;
        font-weight: bold;
        border-bottom: 1px dashed #a7ff1e;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 1100px) {
        .hgnl-grid { grid-template-columns: repeat(2, 1fr); }
        .hgnl-field.full-row { grid-column: span 2; }
    }

    @media (max-width: 768px) {
        .hgnl-grid { grid-template-columns: 1fr; }
        .hgnl-field.full-row { grid-column: span 1; }
        .hgnl-card-wide { padding: 30px; }
    }
</style>

<div class="hgnl-page-container">
    
    @if(!Auth::check())
    <div class="hgnl-logo">
        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Himalaya Trading">
    </div>
    @endif

    <div class="hgnl-card-wide">
        
        @if(session('success') && !Auth::check())
            {{-- 10 SECOND COUNTDOWN FOR GUESTS ONLY --}}
            <div class="success-status-box">
                <h2 style="border:none; padding:0; margin-bottom:15px;">Registration Successful!</h2>
                <p style="color: #a0acb3; font-size: 18px;">{{ session('success') }}</p>
                
                <div class="timer-circle" id="countdown-val">6</div>
                
                <p style="color: #888;">
                    Redirecting to login in <span id="sec-text">6</span> seconds...<br><br>
                    <a href="{{ route('login') }}" class="manual-link">Go to Login Now</a>
                </p>
            </div>

            <script>
                let seconds = 6;
                const display = document.getElementById('countdown-val');
                const text = document.getElementById('sec-text');
                const timer = setInterval(() => {
                    seconds--;
                    display.innerText = seconds;
                    text.innerText = seconds;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        window.location.href = "{{ route('login') }}";
                    }
                }, 1000);
            </script>
        @else
            {{-- STANDARD REGISTRATION CONTENT --}}
            <h2>Member Registration</h2>

            {{-- Logged in Success Message --}}
            @if(session('success'))
                <div style="background: rgba(167, 255, 30, 0.15); border: 1px solid #a7ff1e; color: #a7ff1e; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 600;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- Error Handling --}}
            @if ($errors->any())
                <div style="background: rgba(232, 78, 109, 0.15); border: 1px solid #e84e6d; color: #e84e6d; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                    <ul style="margin:0; padding-left:18px; font-size:14px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('member.store') }}">
                @csrf
                
                <div class="hgnl-grid">
                    {{-- SPONSOR SECTION --}}
                    <div class="hgnl-field">
                        <label>Sponsor ID</label>
                        @if(Auth::check())
                            <input type="text" class="hgnl-input" value="{{ Auth::user()->member_id }}" readonly>
                            <input type="hidden" name="sponsor_id" value="{{ Auth::user()->member_id }}">
                        @else
                            <input type="text" name="sponsor_id" class="hgnl-input" placeholder="Enter Sponsor ID" value="{{ request('ref') }}" required>
                        @endif
                    </div>

                    <div class="hgnl-field">
                        <label>Placement Side (Leg)</label>
                        <select name="position" class="hgnl-select" required>
                            <option value="" disabled selected>Select Side</option>
                            <option value="left" {{ request('pos') == 'left' ? 'selected' : '' }}>Left Leg</option>
                            <option value="right" {{ request('pos') == 'right' ? 'selected' : '' }}>Right Leg</option>
                        </select>
                    </div>

                    <div class="hgnl-field">
                        <label>Full Name</label>
                        <input type="text" name="name" class="hgnl-input" placeholder="e.g. Rahul Sharma" required>
                    </div>

                    {{-- USER INFO SECTION --}}
                    <div class="hgnl-field">
                        <label>Username</label>
                        <input type="text" name="username" class="hgnl-input" placeholder="Unique ID" required>
                    </div>

                    <div class="hgnl-field">
                        <label>Email Address</label>
                        <input type="email" name="email" class="hgnl-input" placeholder="name@domain.com" required>
                    </div>

                    <div class="hgnl-field">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" class="hgnl-input" placeholder="10 Digit Number" required>
                    </div>

                    {{-- LOCATION SECTION --}}
                    <div class="hgnl-field">
                        <label>State</label>
                        <select name="state" class="hgnl-select" required>
                            <option value="" disabled selected>Select State</option>
                            @foreach(['Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Delhi', 'Jammu & Kashmir'] as $state)
                                <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="hgnl-field">
                        <label>City</label>
                        <input type="text" name="city" class="hgnl-input" placeholder="City Name">
                    </div>

                    <div class="hgnl-field">
                        <label>Password</label>
                        <input type="password" name="password" class="hgnl-input" placeholder="••••••••" required>
                    </div>

                    <div class="hgnl-field">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="hgnl-input" placeholder="••••••••" required>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="hgnl-field full-row">
                        <button type="submit" class="hgnl-btn">Complete Registration</button>
                        
                        @if(!Auth::check())
                            <p style="text-align: center; color: #a0acb3; margin-top: 20px;">
                                Already registered? <a href="{{ route('login') }}" style="color: #a7ff1e; text-decoration: none; font-weight: bold;">Login here</a>
                            </p>
                        @endif
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

@endsection