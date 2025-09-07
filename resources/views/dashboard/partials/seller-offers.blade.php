<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Offers on Your Domains</h5>
    </div>
    <div class="card-body">
        @if($offers && count($offers) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Buyer</th>
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
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <span class="text-white fw-bold">
                                                {{ substr($offer->buyer->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $offer->buyer->name }}</div>
                                            <small class="text-muted">{{ $offer->buyer->email }}</small>
                                        </div>
                                    </div>
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
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('domains.show', $offer->domain->slug) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($offer->status === 'pending')
                                            <form method="POST" action="{{ route('offers.accept', $offer->id) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                        onclick="return confirm('Are you sure you want to accept this offer?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('offers.reject', $offer->id) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to reject this offer?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
                <p class="text-muted">You don't have any offers on your domains yet.</p>
            </div>
        @endif
    </div>
</div>
