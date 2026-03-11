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
            <form method="POST" action="{{ route('admin.payments.reject', $row->id) }}" style="display:inline-block">
              @csrf
              <button class="btn-action btn-reject" onclick="return confirm('Reject this payment?')">Reject</button>
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