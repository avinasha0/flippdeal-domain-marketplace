<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Watching</h5>
    </div>
    <div class="card-body">
        @if($watchlist && count($watchlist) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($watchlist as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('domains.show', $item->domain->slug) }}" 
                                       class="text-decoration-none">
                                        {{ $item->domain->full_domain }}
                                    </a>
                                </td>
                                <td>
                                    @if($item->domain->enable_bidding)
                                        <span class="text-success">
                                            Auction: ${{ number_format($item->domain->starting_price, 2) }}
                                        </span>
                                    @elseif($item->domain->bin_price)
                                        <span class="text-primary">
                                            BIN: ${{ number_format($item->domain->bin_price, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            ${{ number_format($item->domain->asking_price, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @switch($item->domain->status)
                                        @case('active')
                                            <span class="badge bg-success">Active</span>
                                            @break
                                        @case('draft')
                                            <span class="badge bg-secondary">Draft</span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($item->domain->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $item->created_at->format('M j, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('domains.show', $item->domain->slug) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('watchlist.toggle', $item->domain->id) }}" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-heart-broken"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Watched Domains</h5>
                <p class="text-muted">You're not watching any domains yet.</p>
            </div>
        @endif
    </div>
</div>
