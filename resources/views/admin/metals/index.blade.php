@extends('layouts.admin')

@section('page_title', 'Metals')

@section('content')
<div style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);gap:2rem;align-items:start">
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Metals List</h2>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Products Count</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($metals as $m)
            <tr>
              <td><strong>{{ $m->name }}</strong></td>
              <td>{{ $m->products_count }} pieces</td>
              <td>
                <form action="{{ route('admin.metals.destroy', $m->id) }}" method="POST" data-confirm="Delete metal '{{ $m->name }}'?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn sm danger" {{ $m->products_count > 0 ? 'disabled' : '' }}>Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Add New Metal</h2>
    </div>

    <form action="{{ route('admin.metals.store') }}" method="POST" class="form">
      @csrf
      <div class="fld">
        <label for="metalName">Metal Name *</label>
        <input class="in" type="text" id="metalName" name="name" placeholder="e.g. 14K Yellow Gold" required>
      </div>

      <button type="submit" class="btn solid" style="justify-self:start;margin-top:.5rem">Save Metal</button>
    </form>
  </div>
</div>
@endsection
