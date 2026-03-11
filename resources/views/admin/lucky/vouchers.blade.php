@extends('common.layout')
@section('title', 'Vouchers')
@section('main')

<style>
/* ==================================================
   Voucher Admin Page – Desktop + Mobile
================================================== */

.card {
    background: linear-gradient(145deg, #0b1220, #020617);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.55);
    color: #e5e7eb;
}

.card h2 {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 24px;
    color: #f8fafc;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Table */
.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 12px;
    color: #e5e7eb;
}

.table thead th {
    padding: 14px 16px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #94a3b8;
    text-align: left;
}

.table tbody tr {
    background: linear-gradient(145deg, #020617, #020617);
    border-radius: 14px;
    transition: all 0.25s ease;
}

.table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(0,0,0,0.45);
}

.table td {
    padding: 16px;
    font-size: 14px;
    vertical-align: middle;
}

.table tbody tr td:first-child {
    border-radius: 14px 0 0 14px;
}
.table tbody tr td:last-child {
    border-radius: 0 14px 14px 0;
}

/* Inputs */
.form-control {
    background: #020617;
    border: 1px solid #1e293b;
    color: #f8fafc;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 13px;
}

.form-control::placeholder {
    color: #64748b;
}

.form-control:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 2px rgba(34,197,94,0.25);
}

/* Green Button */
.btn-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none;
    color: #052e16;
    font-weight: 600;
    border-radius: 10px;
    padding: 10px 16px;
    transition: all 0.25s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #16a34a, #15803d);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(34,197,94,0.45);
}

/* =======================
   MOBILE VIEW
======================= */

@media (max-width: 768px) {

    .card {
        padding: 16px;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        padding: 16px;
        margin-bottom: 18px;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.5);
        transform: none !important;
    }

    .table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(148,163,184,0.15);
    }

    .table td:last-child {
        border-bottom: none;
        padding-top: 14px;
        justify-content: center;
    }

    .table td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .btn-success {
        width: 100%;
        padding: 12px;
        font-size: 14px;
    }
}
</style>

<div class="card">
    <h2>🎫 Vouchers (Month {{ $cycle->current_month }})</h2>

    <table class="table">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Voucher Code</th>
                <th>Month</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($vouchers as $voucher)
            <tr>
                <td data-label="User">{{ $voucher->username }}</td>
                <td data-label="Email">{{ $voucher->email }}</td>
                <td data-label="Mobile">{{ $voucher->mobile }}</td>
                <td data-label="Voucher">{{ $voucher->voucher_code }}</td>
                <td data-label="Month">{{ $voucher->month_no }}</td>
                <td data-label="Action">
                    <form method="POST" action="{{ route('admin.lucky.declare') }}">
                        @csrf
                        <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">

                        <input type="text"
                               name="reward_note"
                               class="form-control mb-2"
                               placeholder="Enter reward / gift won"
                               required>

                        <button class="btn btn-success"
                                onclick="return confirm('Declare this user as winner?')">
                            Declare Winner
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($vouchers->isEmpty())
        <p class="text-muted mt-3">No unused vouchers available.</p>
    @endif
</div>
@endsection
