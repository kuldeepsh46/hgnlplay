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
            <form method="POST" action="{{ route('admin.payouts.reject', $row->id) }}" style="display:inline-block">
              @csrf
              <button class="btn-action btn-reject" onclick="return confirm('Reject this payout?')">Reject</button>
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