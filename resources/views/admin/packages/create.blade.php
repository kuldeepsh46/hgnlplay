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
    .hgnl-input {
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
    @media (max-width: 1100px) { .hgnl-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .hgnl-grid { grid-template-columns: 1fr; } .hgnl-card-wide { padding: 30px; } }
</style>

<div class="hgnl-page-container">
    <div class="hgnl-card-wide">
        <h2>Create New Package</h2>

        <form method="POST" action="{{ route('packages.store') }}">
            @csrf
            <div class="hgnl-grid">
                <div class="hgnl-field">
                    <label>Package Name</label>
                    <input type="text" name="name" class="hgnl-input" placeholder="e.g. Basic Plan" required>
                </div>
                <div class="hgnl-field">
                    <label>Amount (Price)</label>
                    <input type="number" name="amount" class="hgnl-input" placeholder="0" required>
                </div>
                <div class="hgnl-field">
                    <label>PV (Points)</label>
                    <input type="number" name="pv" class="hgnl-input" placeholder="0" required>
                </div>
                <div class="hgnl-field">
                    <label>Direct Bonus</label>
                    <input type="number" name="direct_bonus" class="hgnl-input" placeholder="0" required>
                </div>
                <div class="hgnl-field">
                    <label>Pair Bonus</label>
                    <input type="number" name="pair_bonus" class="hgnl-input" placeholder="0" required>
                </div>
                <div class="hgnl-field full-row">
                    <button type="submit" class="hgnl-btn">Save Package</button>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ route('packages.index') }}" style="color: #a0acb3; text-decoration: none; font-size: 14px;">Cancel and Go Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection