@extends('common.layout')

@section('title', 'Create New Package')

@section('main')
    <style>
        .hgnl-page-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 0;
            box-sizing: border-box;
        }

        .hgnl-card-wide {
            width: 95%;
            max-width: 1400px;
            background-color: #ffffff;
            border: 1px solid #e5f2e6;
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .hgnl-card-wide h2 {
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 40px;
            border-left: 5px solid #23845b;
            padding-left: 20px;
            color: #203c30;
        }

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
            color: #586d63;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .hgnl-input {
            width: 100%;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5f2e6;
            background-color: #f0f8ef;
            color: #203c30;
            font-size: 15px;
            transition: 0.3s;
        }

        .hgnl-input:focus {
            outline: none;
            border-color: #23845b;
            background-color: #ffffff;
        }

        .hgnl-btn {
            background-color: #23845b;
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

        .hgnl-inline {
            display: flex;
            gap: 10px;
        }

        .hgnl-inline .hgnl-input {
            flex: 1 1 auto;
            min-width: 70px;
        }

        .hgnl-inline .hgnl-type {
            flex: 0 0 100px;
            width: 100px;
            padding-left: 12px;
            padding-right: 8px;
        }

        .hgnl-hint {
            color: #586d63;
            font-size: 12px;
            margin-top: 8px;
        }


        .hgnl-pair-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .hgnl-pair-head label {
            margin-bottom: 0;
        }

        .hgnl-pair-tools {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            color: #586d63;
        }

        .hgnl-pair-count {
            color: #23845b;
            font-weight: 700;
        }

        .hgnl-pair-tools button {
            background: none;
            border: none;
            padding: 0;
            color: #586d63;
            font-size: 13px;
            cursor: pointer;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .hgnl-pair-tools button:hover {
            color: #fff;
        }

        .hgnl-pair-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }

        .hgnl-field .hgnl-pair-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            margin: 0;
            border: 1px solid #e5f2e6;
            border-radius: 12px;
            background-color: #f0f8ef;
            color: #203c30;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
            cursor: pointer;
            transition: 0.25s;
        }

        .hgnl-field .hgnl-pair-card:hover {
            border-color: #3a4652;
            background-color: #ffffff;
            transform: translateY(-2px);
        }

        .hgnl-pair-card input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .hgnl-pair-tick {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            border: 2px solid #3a4652;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.25s;
        }

        .hgnl-pair-tick svg {
            width: 14px;
            height: 14px;
            opacity: 0;
            transform: scale(0.5);
            transition: 0.2s;
        }

        .hgnl-pair-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .hgnl-pair-name {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.3;
        }

        .hgnl-pair-amt {
            font-size: 12px;
            color: #586d63;
        }

        .hgnl-pair-card input:checked ~ .hgnl-pair-tick {
            background-color: #23845b;
            border-color: #23845b;
        }

        .hgnl-pair-card input:checked ~ .hgnl-pair-tick svg {
            opacity: 1;
            transform: scale(1);
        }

        .hgnl-pair-card input:checked ~ .hgnl-pair-text .hgnl-pair-amt {
            color: #23845b;
        }

        .hgnl-field .hgnl-pair-card:has(input:checked) {
            border-color: #23845b;
            background-color: rgba(167, 255, 30, 0.06);
            box-shadow: 0 0 0 1px rgba(167, 255, 30, 0.25), 0 8px 20px rgba(167, 255, 30, 0.08);
        }

        .hgnl-pair-card input:focus-visible ~ .hgnl-pair-tick {
            outline: 2px solid #23845b;
            outline-offset: 3px;
        }

        .hgnl-pair-empty {
            padding: 20px;
            border: 1px dashed #e5f2e6;
            border-radius: 12px;
            color: #586d63;
            font-size: 13px;
            text-align: center;
        }

        .hgnl-errors {
            background: rgba(232, 78, 109, 0.1);
            border: 1px solid #b64f70;
            color: #b64f70;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        @media (max-width: 1100px) {
            .hgnl-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hgnl-grid {
                grid-template-columns: 1fr;
            }

            .hgnl-card-wide {
                padding: 30px;
            }
        }
    </style>

    <div class="hgnl-page-container">
        <div class="hgnl-card-wide">
            <h2>Create New Package</h2>

            @if ($errors->any())
                <div class="hgnl-errors">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('packages.store') }}">
                @csrf
                <div class="hgnl-grid">
                    <div class="hgnl-field">
                        <label>Package Name</label>
                        <input type="text" name="name" class="hgnl-input" value="{{ old('name') }}" placeholder="e.g. Basic Plan" required>
                    </div>
                    {{-- <div class="hgnl-field">
                        <label>Amount (Price)</label>
                        <input type="number" name="amount" class="hgnl-input" placeholder="0" required>
                    </div> --}}
                    <div class="hgnl-field">
                        <label>Actual Amount (₹)</label>
                        <input type="number" name="actual_amount" class="hgnl-input" value="{{ old('actual_amount') }}" required>
                    </div>

                    <div class="hgnl-field">
                        <label>Discounted Amount (₹) - First Purchase Only</label>
                        <input type="number" name="discounted_amount" class="hgnl-input" value="{{ old('discounted_amount') }}">
                    </div>
                    <div class="hgnl-field">
                        <label>PV (Points)</label>
                        <input type="number" name="pv" class="hgnl-input" value="{{ old('pv') }}" placeholder="0" required>
                    </div>
                    <div class="hgnl-field">
                        <label>Direct Bonus</label>
                        <div class="hgnl-inline">
                            <input type="number" step="0.01" min="0" name="direct_bonus" class="hgnl-input"
                                value="{{ old('direct_bonus') }}" placeholder="0" required>
                            <select name="direct_bonus_type" class="hgnl-input hgnl-type">
                                <option value="percent" @selected(old('direct_bonus_type', 'percent') === 'percent')>%</option>
                                <option value="fixed" @selected(old('direct_bonus_type') === 'fixed')>₹ Fixed</option>
                            </select>
                        </div>
                    </div>
                    <div class="hgnl-field">
                        <label>Pair Bonus</label>
                        <div class="hgnl-inline">
                            <input type="number" step="0.01" min="0" name="pair_bonus" class="hgnl-input"
                                value="{{ old('pair_bonus') }}" placeholder="0" required>
                            <select name="pair_bonus_type" class="hgnl-input hgnl-type">
                                <option value="percent" @selected(old('pair_bonus_type', 'percent') === 'percent')>%</option>
                                <option value="fixed" @selected(old('pair_bonus_type') === 'fixed')>₹ Fixed</option>
                            </select>
                        </div>
                        <small class="hgnl-hint">% = of matched volume &middot; Fixed = per matched package amount</small>
                    </div>
                    <div class="hgnl-field">
                        <label>Registration Fee (₹100 on member's first purchase)</label>
                        <select name="charges_registration_fee" class="hgnl-input">
                            <option value="1" @selected((string) old('charges_registration_fee', '1') === '1')>Yes — add ₹100 to package price</option>
                            <option value="0" @selected((string) old('charges_registration_fee', '1') === '0')>No — package price is final</option>
                        </select>
                    </div>
                    @include('admin.packages._pairing', ['title' => 'Can Pair With', 'inputName' => 'paired_packages', 'options' => $pairingOptions, 'selected' => old('paired_packages', [])])
                    <div class="hgnl-field full-row">
                        <button type="submit" class="hgnl-btn">Save Package</button>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="{{ route('packages.index') }}"
                                style="color: #586d63; text-decoration: none; font-size: 14px;">Cancel and Go Back</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-pair-group]').forEach(function(group) {
            const boxes = group.querySelectorAll('input[type="checkbox"]');
            const count = group.querySelector('[data-pair-count]');
            if (!boxes.length || !count) return;
            const update = () => count.textContent = [...boxes].filter(b => b.checked).length;
            boxes.forEach(b => b.addEventListener('change', update));
            group.querySelectorAll('[data-pair-all]').forEach(btn => btn.addEventListener('click', () => {
                boxes.forEach(b => b.checked = btn.dataset.pairAll === '1');
                update();
            }));
            update();
        });
    </script>
@endsection
