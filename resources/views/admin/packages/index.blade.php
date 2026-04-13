@extends('common.layout')

@section('title', 'Manage Packages')

@section('main')
    <div class="hgnl-page-container">
        <div class="hgnl-card-wide">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2 style="margin: 0;">Manage Packages</h2>
                <a href="{{ route('packages.create') }}"
                    style="background-color: #a7ff1e; 
          color: #000; 
          font-weight: 800; 
          padding: 10px 20px; 
          border-radius: 8px; 
          text-decoration: none; 
          text-transform: uppercase; 
          font-size: 13px; 
          transition: 0.3s; 
          box-shadow: 0 4px 15px rgba(167, 255, 30, 0.3);"
                    onmouseover="this.style.backgroundColor='#c1ff5e'" onmouseout="this.style.backgroundColor='#a7ff1e'">
                    + Add New Package
                </a>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div
                    style="background: rgba(167, 255, 30, 0.1); border: 1px solid #a7ff1e; color: #a7ff1e; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr
                            style="border-bottom: 2px solid #1b222b; color: #a0acb3; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                            <th style="padding: 15px;">Package Name</th>
                            <th style="padding: 15px;">Amount</th>
                            <th style="padding: 15px;">PV</th>
                            <th style="padding: 15px;">Direct Bonus</th>
                            <th style="padding: 15px;">Pair Bonus</th>
                            <th style="padding: 15px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color: #fff;">
                        @foreach ($packages as $package)
                            <tr style="border-bottom: 1px solid #1b222b; transition: 0.3s;"
                                onmouseover="this.style.backgroundColor='#161d26'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 15px; font-weight: bold;">{{ $package->name }}</td>
                                <td style="padding: 15px; color: #a7ff1e;">₹ {{ number_format($package->amount, 2) }}</td>
                                <td style="padding: 15px;">{{ $package->pv }}</td>
                                <td style="padding: 15px;">{{ $package->direct_bonus }}</td>
                                <td style="padding: 15px;">{{ $package->pair_bonus }}</td>
                                <td style="padding: 15px; text-align: right;">
                                    <div style="display: flex; gap: 15px; justify-content: flex-end;">
                                        <a href="{{ route('packages.edit', $package->id) }}"
                                            style="color: #a0acb3; text-decoration: none; font-size: 14px;">Edit</a>

                                        <form action="{{ route('packages.destroy', $package->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this package?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="background: none; border: none; color: #e84e6d; cursor: pointer; font-size: 14px; padding: 0;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
