@extends('layouts.app')
@section('content')
<style>
/* SECTION BACKGROUND */
.common-section {
    background: #f5e9eb;
    padding: 20px 0;
}
.cards.shadow-sm {
    padding-top: 20px;
}
/* MAIN CONTAINER */
.common-section .container {
    width: 63%;
    background: linear-gradient(180deg, #0f1620, #0b1117);
    border: 1px solid #1f2832;
    border-radius: 20px;
    padding: 35px;
}
.input-custom select {
    padding: 9px;
    font-size: 20px;
    color: #f8f8f8;
    width: 100%;
    border-radius: 10px;
    background: #000;
    border: 1px solid #1f2937;
}
/* INPUTS */
.input-custom {
    margin-bottom: 16px;
}

.input-custom .form-control {
    background: #000;
    border: 1px solid #1f2937;
    color: #e7dede;
    height: 44px;
    border-radius: 8px;
}

.input-custom .form-control::placeholder {
    color: #9ca3af;
}

label {
    color: #cbd5e1;
    font-weight: 500;
    font-size: 18px !important;
}

/* BUTTON */

.logo {
    width: 56px;
    height: 56px;
    margin: 0 auto 14px;
    border-radius: 50%;
    /* background:conic-gradient(from 120deg,#2ee6a6,#a7ff1e,#12d1ff,#2ee6a6); */
    display: grid;
    place-items: center;
    font-weight: 900;
    color: #000;
    box-shadow: 0 0 0 3px #0f141b;
}

.brand h1 {
    font-size: 18px;
    margin: 0;
    letter-spacing: .5px;
    text-align: center;
    margin-bottom: 10px;
}

.brand-green {
    color: #e84e6d;
}

.logo img {
    width: 60px;
    height: 60px;
    display: grid;
    place-items: center;
    color: #06170a;
    font-weight: 900;
}

h2 {
    font-size: 24px;
    margin-bottom: 6px;
}

/* ✅ MOBILE FIX */
@media (max-width: 768px) {
    .common-section .container {
        width: 100% !important;
        padding: 20px !important;
    }
}

@media (max-width: 480px) {
    .common-section {
        padding: 10px !important;
    }

    .common-section .container {
        padding: 16px !important;
        border-radius: 14px;
    }
}
</style>
<section class="common-section login-section reg-section py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="cards shadow-sm">
                  

                    <div class="card-bodys">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Sponsor ID --}}
                            <div class="row custom-row mb-3">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="sponsor_id" class=" col-form-label text-md-end">Sponsor
                                            ID</label>
                                        <div class="input-custom">
                                            <input id="sponsor_id" type="text"
                                                class="form-control @error('sponsor_id') is-invalid @enderror"
                                                name="sponsor_id" value="{{ old('sponsor_id') }}"
                                                placeholder="Enter Sponsor ID">
                                            @error('sponsor_id')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- Sponsor Name (readonly, optional) --}}
                                        <div class="">
                                            <label for="sponsor_name" class="4 col-form-label text-md-end">Sponsor
                                                Name</label>
                                            <div class="input-custom">
                                                <input id="sponsor_name" type="text" class="form-control"
                                                    name="sponsor_name" value="{{ old('sponsor_name') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            {{-- Hidden Position Field --}}
                            <input type="hidden" id="position" name="position" value="">


                            {{-- Name --}}
                            <div class="row mb-3 custom-row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="name" class=" col-form-label text-md-end">Name</label>
                                        <div class="input-custom">
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" required autocomplete="name" autofocus>
                                            @error('name')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- Username --}}
                                        <div class="row mb-3">
                                            <label for="username" class="4 col-form-label text-md-end">Username</label>
                                            <div class="input-custom">
                                                <input id="username" type="text"
                                                    class="form-control @error('username') is-invalid @enderror"
                                                    name="username" value="{{ old('username') }}" required
                                                    autocomplete="username">
                                                @error('username')
                                                <span class="invalid-feedback"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- Email --}}
                                        <div class="row mb-3">
                                            <label for="email" class=" col-form-label text-md-end">Email
                                                Address</label>
                                            <div class="input-custom">
                                                <input id="email" type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email') }}" required
                                                    autocomplete="email">
                                                @error('email')
                                                <span class="invalid-feedback"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--  -->
                                <!--  -->
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- Mobile Number --}}
                                        <div class="row mb-3">
                                            <label for="mobile" class=" col-form-label text-md-end">Mobile
                                                Number</label>
                                            <div class="input-custom">
                                                <input id="mobile" type="text"
                                                    class="form-control @error('mobile') is-invalid @enderror"
                                                    name="mobile" value="{{ old('mobile') }}" required
                                                    pattern="[0-9]{10,11}"
                                                    title="Enter a valid 10–11 digit mobile number">
                                                @error('mobile')
                                                <span class="invalid-feedback"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--  -->


                                <!--  -->
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- PAN Number --}}
                                        <div class="row mb-3">
                                            <label for="pan_number" class="col-form-label text-md-end">PAN
                                                Number (optional)</label>
                                            <div class="input-custom">
                                                <input id="pan_number" type="text"
                                                    class="form-control @error('pan_number') is-invalid @enderror"
                                                    name="pan_number" value="{{ old('pan_number') }}">
                                                @error('pan_number')
                                                <span class="invalid-feedback"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--  -->
                                <div class="col-lg-6 col-12">
                                    <div class="form-grOUP">
                                        <label for="state" class="col-form-label text-md-end">
                                            Select State
                                        </label>

                                        <div class="input-custom">
                                            <select id="state" name="state" class="form-control-select" required>
                                                <option value="">Select State</option>

                                                <!-- States -->
                                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                                <option value="Assam">Assam</option>
                                                <option value="Bihar">Bihar</option>
                                                <option value="Chhattisgarh">Chhattisgarh</option>
                                                <option value="Goa">Goa</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Haryana">Haryana</option>
                                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                                <option value="Jharkhand">Jharkhand</option>
                                                <option value="Karnataka">Karnataka</option>
                                                <option value="Kerala">Kerala</option>
                                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                                <option value="Maharashtra">Maharashtra</option>
                                                <option value="Manipur">Manipur</option>
                                                <option value="Meghalaya">Meghalaya</option>
                                                <option value="Mizoram">Mizoram</option>
                                                <option value="Nagaland">Nagaland</option>
                                                <option value="Odisha">Odisha</option>
                                                <option value="Punjab">Punjab</option>
                                                <option value="Rajasthan">Rajasthan</option>
                                                <option value="Sikkim">Sikkim</option>
                                                <option value="Tamil Nadu">Tamil Nadu</option>
                                                <option value="Telangana">Telangana</option>
                                                <option value="Tripura">Tripura</option>
                                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                                <option value="Uttarakhand">Uttarakhand</option>
                                                <option value="West Bengal">West Bengal</option>

                                                <!-- Union Territories -->
                                                <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                                                <option value="Chandigarh">Chandigarh</option>
                                                <option value="Dadra and Nagar Haveli and Daman and Diu">
                                                    Dadra and Nagar Haveli and Daman and Diu
                                                </option>
                                                <option value="Delhi">Delhi</option>
                                                <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                                                <option value="Ladakh">Ladakh</option>
                                                <option value="Lakshadweep">Lakshadweep</option>
                                                <option value="Puducherry">Puducherry</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!--  -->

                                <div class="col-lg-12 col-12">
                                    <div class="form-group">
                                        {{-- City --}}
                                        <div class="row mb-3">
                                            <label for="city" class="col-form-label text-md-end">Enter
                                                City</label>
                                            <div class="input-custom">
                                                <input id="city" type="text" class="form-control" name="city"
                                                    placeholder="Enter City">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                                <!--  -->

                                <!-- <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- City --}}
                                        <div class="row mb-3">
                                            <label for="city" class="col-form-label text-md-end">Select
                                                City</label>
                                            <div class="input-custom">
                                                <input id="city" type="text" class="form-control" name="city"
                                                    placeholder="Enter City">
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <!--  -->
                                <!--  -->

                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- Password --}}
                                        <div class="row mb-3">
                                            <label for="password"
                                                class=" col-form-label text-md-end">Password</label>
                                            <div class="input-custom">
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="new-password">
                                                @error('password')
                                                <span class="invalid-feedback"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->

                                <!--  -->

                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        {{-- Confirm Password --}}
                                        <div class="row mb-3">
                                            <label for="password-confirm"
                                                class=" col-form-label text-md-end">Confirm
                                                Password</label>
                                            <div class="input-custom">
                                                <input id="password-confirm" type="password" class="form-control"
                                                    name="password_confirmation" required autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                            </div>

                            <!--  -->
                            <!--  -->

                    </div>
                    <!--  -->
                    <!--  -->

                 


                </div>
                <!--  -->


            </div>
            <!--  -->
            <!--  -->

        </div>
        <!--  -->

        {{-- Agreement --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input me-2" id="agreement" required>
                    <label class="form-check-label" for="agreement">
                        I accept the <a href="#">User Agreement</a>
                    </label>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="row mb-0 justify-content-center">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary w-100">
                    Register
                </button>
            </div>
        </div>
    </div>







    </form>
    </div>
    </div>
    </div>
    </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
  // ✅ Parse URL parameters
  const params = new URLSearchParams(window.location.search);
  const refid = params.get("refid");
  const leg = params.get("leg");
  const name = params.get("name");

  // ✅ Fill Sponsor ID
  if (refid) {
    const sponsorIdInput = document.getElementById("sponsor_id");
    sponsorIdInput.value = refid;
    sponsorIdInput.readOnly = true;
  }

  // ✅ Fill Sponsor Name
  if (name) {
    const sponsorNameInput = document.getElementById("sponsor_name");
    sponsorNameInput.value = decodeURIComponent(name);
    sponsorNameInput.readOnly = true;
  }

  // ✅ Set hidden input for position
  const positionInput = document.getElementById("position");
  if (positionInput && leg) {
    if (parseInt(leg) === 1) {
      positionInput.value = "left";
    } else if (parseInt(leg) === 2) {
      positionInput.value = "right";
    }
  }
});
</script>

@endsection