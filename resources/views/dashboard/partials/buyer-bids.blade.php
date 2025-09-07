<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Your Bids</h5>
    </div>
    <div class="card-body">
        @if($bids && count($bids) > 0)
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
                        @foreach($bids as $bid)
                            <tr>
                                <td>
                                    <a href="{{ route('domains.show', $bid->domain->slug) }}" 
                                       class="text-decoration-none">
                                        {{ $bid->domain->full_domain }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        ${{ number_format($bid->amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($bid->is_winning)
                                        <span class="badge bg-success">Winning</span>
                                    @else
                                        <span class="badge bg-secondary">Outbid</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $bid->created_at->format('M j, Y g:i A') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('domains.show', $bid->domain->slug) }}" 
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
                <i class="fas fa-gavel fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Bids Yet</h5>
                <p class="text-muted">You haven't placed any bids yet.</p>
            </div>
        @endif
    </div>
</div>
