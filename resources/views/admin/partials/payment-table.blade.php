<div class="table-res">
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>User</th>
      <th>Email</th>
      <th>Amount</th>
      <th>Payment Method</th>
      <th>Bank</th>
      <th>Account</th>
      <th>Txn Remark</th>
      <th>Attachment</th>
      <th>Status</th>
      <th>Date</th>
      @if(isset($pending))<th>Action</th>@endif
    </tr>
  </thead>

  <tbody>
    @forelse($data as $i => $row)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $row->username }}</td>
        <td>{{ $row->email }}</td>
        <td>₹{{ number_format($row->amount, 2) }}</td>
        <td>{{ $row->payment_mode }}</td>
        <td>{{ $row->bank_name }}</td>
        <td>{{ $row->account_number }}</td>
        <td>{{ $row->transaction_remark }}</td>

        {{-- ✅ Attachment column --}}
        <td>
          @if($row->attachment)
            <button class="btn-action btn-view"
              onclick="viewAttachment('{{ asset($row->attachment) }}')">View</button>
          @else
            <span style="color:#777;">No File</span>
          @endif
        </td>

        <td style="color:
          {{ $row->status == 'completed' ? '#a7ff1e' : ($row->status == 'pending' ? '#ffc107' : '#ff4a4a') }}">
          {{ ucfirst($row->status) }}
        </td>

        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') }}</td>

        {{-- ✅ Action buttons only for pending payments --}}
        @if(isset($pending) && $row->status === 'pending')
          <td>
            <form method="POST" action="{{ route('admin.payments.approve', $row->id) }}" style="display:inline-block">
              @csrf
              <button class="btn-action btn-approve" onclick="return confirm('Approve this payment?')">Approve</button>
            </form>
            {{-- <form method="POST" action="{{ route('admin.payments.reject', $row->id) }}" style="display:inline-block">
              @csrf
              <button class="btn-action btn-reject" onclick="return confirm('Reject this payment?')">Reject</button>
            </form> --}}

         <form method="POST"
      action="{{ route('admin.payments.reject', $row->id) }}"
      style="display:inline-block; vertical-align:top;"
      id="paymentRejectForm{{ $row->id }}">

    @csrf

    <button type="button"
            class="btn-action btn-reject"
            style="display:inline-block;"
            onclick="showPaymentRejectBox({{ $row->id }})">
        Reject
    </button>

    <div id="paymentRejectBox{{ $row->id }}"
         style="display:none; margin-top:10px; width:300px; background:#fff; border:1px solid #ddd; padding:10px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

        <textarea name="reject_reason"
                  id="paymentRejectReason{{ $row->id }}"
                  rows="3"
                  placeholder="Enter rejection reason..."
                  style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; resize:vertical;"
                  required></textarea>

        <div style="margin-top:8px; display:flex; gap:8px;">
            <button type="submit"
                    class="btn-action btn-reject"
                    onclick="return validatePaymentRejectReason({{ $row->id }})">
                Confirm Reject
            </button>

            <button type="button"
                    class="btn-action"
                    style="background:#6c757d; color:#fff;"
                    onclick="hidePaymentRejectBox({{ $row->id }})">
                Cancel
            </button>
        </div>
    </div>
</form>
          </td>
        @endif
      </tr>
    @empty
      <tr><td colspan="12">No records found.</td></tr>
    @endforelse
  </tbody>
</table>
</div>

<script>
    function showPaymentRejectBox(id) {
        const box = document.getElementById('paymentRejectBox' + id);
        const textarea = document.getElementById('paymentRejectReason' + id);

        if (box) {
            box.style.display = 'block';
        }

        if (textarea) {
            textarea.focus();
        }
    }

    function hidePaymentRejectBox(id) {
        const box = document.getElementById('paymentRejectBox' + id);
        const textarea = document.getElementById('paymentRejectReason' + id);

        if (textarea) {
            textarea.value = '';
        }

        if (box) {
            box.style.display = 'none';
        }
    }

    function validatePaymentRejectReason(id) {
        const textarea = document.getElementById('paymentRejectReason' + id);

        if (!textarea || textarea.value.trim() === '') {
            alert('Please enter rejection reason.');
            if (textarea) {
                textarea.focus();
            }
            return false;
        }

        return confirm('Are you sure you want to reject this payment?');
    }
</script>