@extends('common.layout')
@section('title', 'Lucky Draw Participants')
@section('main')


<style>
/* ==================================================
   Lucky Draw Admin Panel – Desktop + Mobile
================================================== */

/* Card Container */
.card {
    background: linear-gradient(145deg, #0b1220, #020617);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.55);
    color: #e5e7eb;
}

/* Heading */
.card h2 {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 24px;
    color: #f8fafc;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* =======================
   DESKTOP TABLE
======================= */

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
    background: transparent;
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

/* Rounded row edges */
.table tbody tr td:first-child {
    border-radius: 14px 0 0 14px;
}
.table tbody tr td:last-child {
    border-radius: 0 14px 14px 0;
}

/* Status badge */
.badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.bg-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #052e16;
}

/* Buttons */
.btn {
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    transition: all 0.25s ease;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #ffffff;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37,99,235,0.45);
}

/* =======================
   MOBILE VIEW – CARD STYLE
======================= */

@media (max-width: 768px) {

    .card {
        padding: 16px;
    }

    /* Hide table head */
    .table thead {
        display: none;
    }

    /* Each row becomes a card */
    .table tbody tr {
        display: block;
        padding: 16px;
        margin-bottom: 18px;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.5);
        transform: none !important;
    }

    .table tbody tr:hover {
        transform: none;
    }

    /* Each cell becomes label/value row */
    .table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-radius: 0 !important;
        font-size: 14px;
        border-bottom: 1px solid rgba(148,163,184,0.15);
    }

    .table td:last-child {
        border-bottom: none;
        padding-top: 14px;
        justify-content: center;
    }

    /* Label */
    .table td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.4px;
    }

    /* Badge center */
    .badge {
        padding: 6px 16px;
        font-size: 12px;
    }

    /* Button full width */
    .btn {
        width: 100%;
        padding: 12px;
        font-size: 14px;
    }
}

</style>
<div class="card">
    <h2>🎟 Lucky Draw Participants</h2> 
     @php
          $lastRelease = \DB::table('lucky_release_logs')->latest('released_at')->first();
          $isDisabled = $lastRelease && 
              \Carbon\Carbon::parse($lastRelease->released_at)->diffInDays(now()) < 30;
      @endphp

      <form method="POST" action="{{ url('/lucky/release') }}">
          @csrf
          <button type="submit"
              {{ $isDisabled ? 'disabled' : '' }}
              style="
                  padding: 10px 18px;
                  border-radius: 10px;
                  border: none;
                  font-weight: 600;
                  font-size: 14px;
                  cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }};
                  background: {{ $isDisabled ? '#1f2933' : '#16a34a' }};
                  color: {{ $isDisabled ? '#6b7280' : '#ffffff' }};
                  box-shadow: {{ $isDisabled ? 'none' : '0 6px 18px rgba(22,163,74,0.45)' }};
                  transition: all 0.25s ease;
              ">
              Release Monthly Vouchers
          </button>
      </form>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Package</th>
                <th>Month</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($participants as $p)
            <tr>
                <td data-label="User">{{ $p->name }}</td>
<td data-label="Email">{{ $p->email }}</td>
<td data-label="Package">{{ $p->package_id == 4 ? '₹50,000' : '₹1,00,000' }}</td>
<td data-label="Month">{{ $p->current_month }} / 16</td>
<td data-label="Status"><span class="badge bg-success">Active</span></td>
<td data-label="Action">
    <a href="{{ route('admin.lucky.vouchers', $p->cycle_id) }}" class="btn btn-primary">
        View Vouchers
    </a>
</td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
