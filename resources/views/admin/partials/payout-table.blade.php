<div class="table-res">
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>User</th>
      <th>Email</th>
      <th>Wallet Balance</th>
      <th>Requested Amount</th>
      <th>Tax</th>
      <th>Net Amount</th>
      <th>Bank</th>
      <th>Account No</th>
      <th>IFSC</th>
      <th>Status</th>
      <th>Date</th>
      @if(isset($pending))<th>Action</th>@endif
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $row->username ?? $row->name }}</td>
        <td>{{ $row->email }}</td>
        <td>₹{{ number_format($row->wallet_balance, 2) }}</td>
        <td>₹{{ number_format($row->amount, 2) }}</td>
        <td>₹{{ number_format($row->tax_amount, 2) }}</td>
        <td>₹{{ number_format($row->net_amount, 2) }}</td>
        <td>{{ $row->bank_name ?? '-' }}</td>
        <td>{{ $row->account_number ?? '-' }}</td>
        <td>{{ $row->ifsc_code ?? '-' }}</td>
        <td style="color:
          {{ $row->status == 'completed' ? '#a7ff1e' : ($row->status == 'pending' ? '#ffc107' : '#ff4a4a') }}">
          {{ ucfirst($row->status) }}
        </td>
        <td>
        {{ \Carbon\Carbon::parse($row->updated_at ?? $row->created_at)->format('d M Y, h:i A') }}
        </td>

        @if(isset($pending) && $row->status == 'pending')
          <td>
            <form method="POST" action="{{ route('admin.payouts.approve', $row->id) }}" style="display:inline-block">
              @csrf
              <button class="btn-action btn-approve" onclick="return confirm('Approve this payout?')">Approve</button>
            </form>
            {{-- <form method="POST" action="{{ route('admin.payouts.reject', $row->id) }}" style="display:inline-block">
              @csrf
              <button class="btn-action btn-reject" onclick="return confirm('Reject this payout?')">Reject</button>
            </form> --}}
            <form method="POST"
      action="{{ route('admin.payouts.reject', $row->id) }}"
      style="display:inline-block; vertical-align:top;"
      id="payoutRejectForm{{ $row->id }}">

    @csrf

    <button type="button"
            class="btn-action btn-reject"
            onclick="showPayoutRejectBox({{ $row->id }})">
        Reject
    </button>

    <div id="payoutRejectBox{{ $row->id }}"
         style="display:none; margin-top:10px; width:300px; background:#fff; border:1px solid #ddd; padding:10px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

        <textarea name="reject_reason"
                  id="payoutRejectReason{{ $row->id }}"
                  rows="3"
                  placeholder="Enter rejection reason..."
                  style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; resize:vertical;"
                  required></textarea>

        <div style="margin-top:8px; display:flex; gap:8px;">
            <button type="submit"
                    class="btn-action btn-reject"
                    onclick="return validatePayoutRejectReason({{ $row->id }})">
                Confirm Reject
            </button>

            <button type="button"
                    class="btn-action"
                    style="background:#6c757d; color:#fff;"
                    onclick="hidePayoutRejectBox({{ $row->id }})">
                Cancel
            </button>
        </div>
    </div>
</form>
          </td>
        @endif
      </tr>
    @empty
      <tr><td colspan="13">No records found.</td></tr>
    @endforelse
  </tbody>
</table>
</div>
<script>
    function showPayoutRejectBox(id) {
        const box = document.getElementById('payoutRejectBox' + id);
        const textarea = document.getElementById('payoutRejectReason' + id);

        if (box) {
            box.style.display = 'block';
        }

        if (textarea) {
            textarea.focus();
        }
    }

    function hidePayoutRejectBox(id) {
        const box = document.getElementById('payoutRejectBox' + id);
        const textarea = document.getElementById('payoutRejectReason' + id);

        if (textarea) {
            textarea.value = '';
        }

        if (box) {
            box.style.display = 'none';
        }
    }

    function validatePayoutRejectReason(id) {
        const textarea = document.getElementById('payoutRejectReason' + id);

        if (!textarea || textarea.value.trim() === '') {
            alert('Please enter rejection reason.');
            if (textarea) {
                textarea.focus();
            }
            return false;
        }

        return confirm('Are you sure you want to reject this payout?');
    }
</script>