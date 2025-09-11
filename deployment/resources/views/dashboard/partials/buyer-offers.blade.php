<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Your Offers</h5>
    </div>
    <div class="card-body">
        @if($offers && count($offers) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offers as $offer)
                            <tr>
                                <td>
                                    <a href="{{ route('domains.show', $offer->domain->slug) }}" 
                                       class="text-decoration-none">
                                        {{ $offer->domain->full_domain }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">
                                        ${{ number_format($offer->amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @switch($offer->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('accepted')
                                            <span class="badge bg-success">Accepted</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-secondary">Expired</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($offer->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $offer->created_at->format('M j, Y g:i A') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('domains.show', $offer->domain->slug) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Offers Yet</h5>
                <p class="text-muted">You haven't made any offers yet.</p>
            </div>
        @endif
    </div>
</div>
