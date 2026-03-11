<div class="table-res">
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>User</th>
      <th>Email</th>
      <th>Subject</th>
      <th>Status</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $row->username ?? 'N/A' }}</td>
        <td>{{ $row->email }}</td>
        <td>{{ $row->subject }}</td>
        <td style="color:{{ $row->status == 'Responded' ? '#a7ff1e' : '#ffc107' }}">{{ $row->status }}</td>
        <td>{{ \Carbon\Carbon::parse($row->updated_at ?? $row->created_at)->format('d M Y, h:i A') }}</td>
        <td><button class="btn-view" onclick="openModal({{ $row->id }})">View Message</button></td>
      </tr>
    @empty
      <tr><td colspan="7">No records found.</td></tr>
    @endforelse
  </tbody>
</table>
</div>
