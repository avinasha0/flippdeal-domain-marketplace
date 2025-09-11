<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Messages</h5>
    </div>
    <div class="card-body">
        @if($conversations && count($conversations) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Buyer</th>
                            <th>Domain</th>
                            <th>Last Message</th>
                            <th>Unread</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conversations as $conversation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <span class="text-white fw-bold">
                                                {{ substr($conversation->buyer->name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $conversation->buyer->name ?? 'Unknown' }}</div>
                                            <small class="text-muted">{{ $conversation->buyer->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($conversation->domain)
                                        <a href="{{ route('domains.show', $conversation->domain->slug) }}" 
                                           class="text-decoration-none">
                                            {{ $conversation->domain->full_domain }}
                                        </a>
                                    @else
                                        <span class="text-muted">General</span>
                                    @endif
                                </td>
                                <td>
                                    @if($conversation->latestMessage)
                                        <div class="text-truncate" style="max-width: 200px;">
                                            {{ $conversation->latestMessage->message ?? $conversation->latestMessage->body ?? 'No message' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $conversation->latestMessage->created_at->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="text-muted">No messages</span>
                                    @endif
                                </td>
                                <td>
                                    @if($conversation->seller_unread_count > 0)
                                        <span class="badge bg-danger">{{ $conversation->seller_unread_count }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('conversations.show', $conversation->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-comments"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Messages Yet</h5>
                <p class="text-muted">You don't have any conversations with buyers yet.</p>
            </div>
        @endif
    </div>
</div>
