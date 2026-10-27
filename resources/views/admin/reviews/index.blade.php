@extends('layouts.admin')

@section('page_title', 'Reviews')

@section('content')
<div class="admin-card">
  <div class="admin-card-h">
    <h2>Customer Reviews</h2>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Piece</th>
          <th>Rating</th>
          <th>Review</th>
          <th>Author</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $rev)
          <tr>
            <td>
              <strong>{{ $rev->product->name ?? 'Deleted product' }}</strong>
            </td>
            <td>
              <span class="chip-rate" style="padding:.1rem .5rem;font-size:.8rem">
                ★ {{ $rev->rating }}
              </span>
            </td>
            <td style="max-width:300px">
              @if($rev->title)
                <strong>{{ $rev->title }}</strong><br>
              @endif
              <span>{{ $rev->body }}</span>
            </td>
            <td>{{ $rev->user->name ?? 'Customer' }}</td>
            <td class="mono" style="color:var(--muted)">{{ $rev->created_at->format('d M Y') }}</td>
            <td>
              <span class="badge {{ $rev->is_approved ? 'success' : 'warning' }}">
                {{ $rev->is_approved ? 'Approved' : 'Hidden' }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:.4rem">
                <form action="{{ route('admin.reviews.approve', $rev->id) }}" method="POST">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn sm">
                    {{ $rev->is_approved ? 'Hide' : 'Approve' }}
                  </button>
                </form>

                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" data-confirm="Delete this review?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn sm danger">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;padding:2.5rem" class="muted">No reviews submitted yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:1.2rem">
    {{ $reviews->links() }}
  </div>
</div>
@endsection
