<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Bids on Your Domains</h5>
    </div>
    <div class="card-body">
        @if($bids && count($bids) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Bidder</th>
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
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <span class="text-white fw-bold">
                                                {{ substr($bid->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $bid->user->name }}</div>
                                            <small class="text-muted">{{ $bid->user->email }}</small>
                                        </div>
                                    </div>
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
                <p class="text-muted">You don't have any bids on your domains yet.</p>
            </div>
        @endif
    </div>
</div>
