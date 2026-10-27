@extends('layouts.admin')

@section('page_title', 'Inbox & Newsletter')

@section('content')
<div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:2rem;align-items:start">
  <!-- Contact Messages -->
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Contact Messages</h2>
      <a href="{{ route('admin.inbox.exportMessages') }}" class="btn sm">Export Messages CSV ↓</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Sender</th>
            <th>Message</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($messages as $msg)
            <tr style="{{ ! $msg->is_read ? 'background:rgba(216,27,102,0.04);' : '' }}">
              <td class="mono" style="color:var(--muted)">{{ $msg->created_at->format('d M, h:i A') }}</td>
              <td>
                <strong>{{ $msg->name }}</strong><br>
                <small class="mono">{{ $msg->email }}</small><br>
                @if($msg->phone)
                  <small class="mono" style="color:var(--muted)">{{ $msg->phone }}</small>
                @endif
              </td>
              <td style="max-width:280px">
                <p style="margin:0;line-height:1.4">{{ $msg->message }}</p>
              </td>
              <td>
                <span class="badge {{ $msg->is_read ? 'muted' : 'danger' }}">
                  {{ $msg->is_read ? 'Read' : 'Unread' }}
                </span>
              </td>
              <td>
                <form action="{{ route('admin.inbox.read', $msg->id) }}" method="POST">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn sm">
                    {{ $msg->is_read ? 'Mark Unread' : 'Mark Read' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align:center;padding:2rem" class="muted">No messages received yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1.2rem">
      {{ $messages->links() }}
    </div>
  </div>

  <!-- Newsletter Subscribers -->
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Newsletter Subscribers ({{ \App\Models\NewsletterSubscriber::count() }})</h2>
      <a href="{{ route('admin.inbox.exportSubscribers') }}" class="btn sm">Export CSV ↓</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Email</th>
            <th>Subscribed Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subscribers as $sub)
            <tr>
              <td><b class="mono">{{ $sub->email }}</b></td>
              <td class="mono" style="color:var(--muted)">
                {{ $sub->subscribed_at ? $sub->subscribed_at->format('d M Y') : $sub->created_at->format('d M Y') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" style="text-align:center;padding:2rem" class="muted">No subscribers yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1.2rem">
      {{ $subscribers->links() }}
    </div>
  </div>
</div>
@endsection
