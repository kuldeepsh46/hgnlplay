@extends('common.layout')

@section('title', 'Edit Package')

@section('main')
<style>
    /* ... Copy the exact same <style> block from above ... */
    .hgnl-page-container { width: 100%; display: flex; flex-direction: column; align-items: center; padding: 40px 0; box-sizing: border-box; }
    .hgnl-card-wide { width: 95%; max-width: 1400px; background-color: #10171f; border: 1px solid #1b222b; border-radius: 16px; padding: 50px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
    .hgnl-card-wide h2 { font-size: 28px; margin-top: 0; margin-bottom: 40px; border-left: 5px solid #a7ff1e; padding-left: 20px; color: #fff; }
    .hgnl-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
    .hgnl-field { display: flex; flex-direction: column; }
    .hgnl-field.full-row { grid-column: span 3; }
    .hgnl-field label { font-size: 12px; text-transform: uppercase; color: #a0acb3; margin-bottom: 10px; font-weight: 700; letter-spacing: 1px; }
    .hgnl-input { width: 100%; padding: 16px; border-radius: 8px; border: 1px solid #1b222b; background-color: #0b0e12; color: #fff; font-size: 15px; transition: 0.3s; }
    .hgnl-input:focus { outline: none; border-color: #a7ff1e; background-color: #0d1218; }
    .hgnl-btn { background-color: #a7ff1e; color: #000; font-weight: 800; border: none; border-radius: 10px; padding: 20px; font-size: 16px; text-transform: uppercase; cursor: pointer; transition: 0.3s; margin-top: 20px; }
    .hgnl-btn:hover { background-color: #c1ff5e; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(167, 255, 30, 0.2); }
    .hgnl-inline { display: flex; gap: 10px; }
    .hgnl-inline .hgnl-input { flex: 1 1 auto; min-width: 70px; }
    .hgnl-inline .hgnl-type { flex: 0 0 100px; width: 100px; padding-left: 12px; padding-right: 8px; }
    .hgnl-hint { color: #6c7a85; font-size: 12px; margin-top: 8px; }
    .hgnl-errors { background: rgba(232, 78, 109, 0.1); border: 1px solid #e84e6d; color: #e84e6d; padding: 15px; border-radius: 8px; margin-bottom: 25px; }
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
        color: #6c7a85;
    }

    .hgnl-pair-count {
        color: #a7ff1e;
        font-weight: 700;
    }

    .hgnl-pair-tools button {
        background: none;
        border: none;
        padding: 0;
        color: #a0acb3;
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
        border: 1px solid #1b222b;
        border-radius: 12px;
        background-color: #0b0e12;
        color: #fff;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
        cursor: pointer;
        transition: 0.25s;
    }

    .hgnl-field .hgnl-pair-card:hover {
        border-color: #3a4652;
        background-color: #0d1218;
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
        color: #6c7a85;
    }

    .hgnl-pair-card input:checked ~ .hgnl-pair-tick {
        background-color: #a7ff1e;
        border-color: #a7ff1e;
    }

    .hgnl-pair-card input:checked ~ .hgnl-pair-tick svg {
        opacity: 1;
        transform: scale(1);
    }

    .hgnl-pair-card input:checked ~ .hgnl-pair-text .hgnl-pair-amt {
        color: #a7ff1e;
    }

    .hgnl-field .hgnl-pair-card:has(input:checked) {
        border-color: #a7ff1e;
        background-color: rgba(167, 255, 30, 0.06);
        box-shadow: 0 0 0 1px rgba(167, 255, 30, 0.25), 0 8px 20px rgba(167, 255, 30, 0.08);
    }

    .hgnl-pair-card input:focus-visible ~ .hgnl-pair-tick {
        outline: 2px solid #a7ff1e;
        outline-offset: 3px;
    }

    .hgnl-pair-empty {
        padding: 20px;
        border: 1px dashed #1b222b;
        border-radius: 12px;
        color: #6c7a85;
        font-size: 13px;
        text-align: center;
    }
</style>

<div class="hgnl-page-container">
    <div class="hgnl-card-wide">
        <h2>Edit Package: {{ $package->name }}</h2>

        @if ($errors->any())
            <div class="hgnl-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('packages.update', $package->id) }}">
            @csrf
            @method('PUT')
            <div class="hgnl-grid">
                <div class="hgnl-field">
                    <label>Package Name</label>
                    <input type="text" name="name" class="hgnl-input" value="{{ old('name', $package->name) }}" required>
                </div>
                <div class="hgnl-field">
                    <label>Amount (Price)</label>
                    <input type="number" name="amount" class="hgnl-input" value="{{ old('amount', $package->amount) }}" required>
                </div>
                <div class="hgnl-field">
                    <label>PV (Points)</label>
                    <input type="number" name="pv" class="hgnl-input" value="{{ old('pv', $package->pv) }}" required>
                </div>
                <div class="hgnl-field">
                    <label>Direct Bonus</label>
                    <div class="hgnl-inline">
                        <input type="number" step="0.01" min="0" name="direct_bonus" class="hgnl-input" value="{{ old('direct_bonus', $package->direct_bonus) }}" required>
                        <select name="direct_bonus_type" class="hgnl-input hgnl-type">
                            <option value="percent" @selected(old('direct_bonus_type', $package->direct_bonus_type) === 'percent')>%</option>
                            <option value="fixed" @selected(old('direct_bonus_type', $package->direct_bonus_type) === 'fixed')>₹ Fixed</option>
                        </select>
                    </div>
                </div>
                <div class="hgnl-field">
                    <label>Pair Bonus</label>
                    <div class="hgnl-inline">
                        <input type="number" step="0.01" min="0" name="pair_bonus" class="hgnl-input" value="{{ old('pair_bonus', $package->pair_bonus) }}" required>
                        <select name="pair_bonus_type" class="hgnl-input hgnl-type">
                            <option value="percent" @selected(old('pair_bonus_type', $package->pair_bonus_type) === 'percent')>%</option>
                            <option value="fixed" @selected(old('pair_bonus_type', $package->pair_bonus_type) === 'fixed')>₹ Fixed</option>
                        </select>
                    </div>
                    <small class="hgnl-hint">% = of matched volume &middot; Fixed = per matched package amount</small>
                </div>
                <div class="hgnl-field full-row">
                    <div class="hgnl-pair-head">
                        <label>Can Pair With</label>
                        @if ($allPackages->isNotEmpty())
                            <div class="hgnl-pair-tools">
                                <span><span class="hgnl-pair-count" id="pairCount">0</span> selected</span>
                                <button type="button" data-pair-all="1">Select all</button>
                                <button type="button" data-pair-all="0">Clear</button>
                            </div>
                        @endif
                    </div>
                    @if ($allPackages->isNotEmpty())
                        <div class="hgnl-pair-grid">
                            @foreach ($allPackages as $p)
                                <label class="hgnl-pair-card">
                                    <input type="checkbox" name="paired_packages[]" value="{{ $p->id }}"
                                        @checked(in_array($p->id, old('paired_packages', $pairedIds)))>
                                    <span class="hgnl-pair-tick">
                                        <svg viewBox="0 0 16 16" fill="none" stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8.5l3.2 3L13 5" /></svg>
                                    </span>
                                    <span class="hgnl-pair-text">
                                        <span class="hgnl-pair-name">{{ $p->name }}</span>
                                        <span class="hgnl-pair-amt">₹{{ number_format($p->actual_amount) }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="hgnl-pair-empty">No other packages yet.</div>
                    @endif
                </div>
                <div class="hgnl-field full-row">
                    <button type="submit" class="hgnl-btn">Update Package Details</button>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ route('packages.index') }}" style="color: #a0acb3; text-decoration: none; font-size: 14px;">Cancel and Go Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <script>
        (function() {
            const boxes = document.querySelectorAll('input[name="paired_packages[]"]');
            const count = document.getElementById('pairCount');
            if (!boxes.length || !count) return;
            const update = () => count.textContent = [...boxes].filter(b => b.checked).length;
            boxes.forEach(b => b.addEventListener('change', update));
            document.querySelectorAll('[data-pair-all]').forEach(btn => btn.addEventListener('click', () => {
                boxes.forEach(b => b.checked = btn.dataset.pairAll === '1');
                update();
            }));
            update();
        })();
    </script>
@endsection
