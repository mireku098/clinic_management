<!-- Results Grid -->
<div class="row">
  @forelse ($results as $result)
    <div class="col-lg-6 mb-4">
      <div class="card h-100 result-card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="result-type-icon result-type-{{ $result->result_type }}">
              @if ($result->result_type === 'text')
                <i class="fas fa-file-alt"></i>
              @elseif ($result->result_type === 'numeric')
                <i class="fas fa-calculator"></i>
              @elseif ($result->result_type === 'file')
                <i class="fas fa-file-pdf"></i>
              @endif
            </div>
            <span class="badge status-badge bg-{{ $result->status === 'approved' ? 'success' : ($result->status === 'pending_approval' ? 'warning' : ($result->status === 'rejected' ? 'danger' : 'secondary')) }}">
              {{ ucfirst(str_replace('_', ' ', $result->status)) }}
            </span>
          </div>
          
          <!-- Service/Package Name -->
          <h6 class="card-title">
            @if($result->package)
              <span class="badge bg-primary me-2">
                <i class="fas fa-box me-1"></i>Package
              </span>
              {{ $result->package->package_name }}
            @elseif($result->service)
              {{ $result->service->service_name }}
            @else
              Unknown Service/Package
            @endif
          </h6>
          
          <p class="card-text text-muted small mb-2">
            <i class="fas fa-user me-1"></i> {{ $result->patient->first_name }} {{ $result->patient->last_name }}
            @if ($result->visit)
              <span class="ms-2"><i class="fas fa-calendar me-1"></i> Visit #{{ $result->visit->id }}</span>
            @endif
          </p>
          
          <div class="result-value mb-3">
            @if ($result->result_type === 'text')
              <p class="mb-0">{{ Str::limit($result->result_text, 100) }}</p>
            @elseif ($result->result_type === 'numeric')
              <h5 class="text-primary mb-0">{{ $result->result_numeric }}</h5>
            @elseif ($result->result_type === 'file')
              <a href="{{ asset('storage/' . $result->result_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-download me-1"></i> {{ $result->result_file_name }}
              </a>
            @endif
          </div>
          
          @if ($result->notes)
            <p class="card-text text-muted small mb-3">
              <i class="fas fa-sticky-note me-1"></i> {{ Str::limit($result->notes, 80) }}
            </p>
          @endif
          
          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
              <i class="fas fa-clock me-1"></i> {{ optional($result->created_at)->format('M d, Y H:i') }}
            </small>
            <div class="btn-group btn-group-sm">
              <a href="{{ route('service-results.show', $result->id) }}" class="btn btn-outline-primary" title="View">
                <i class="fas fa-eye"></i>
              </a>
              
              @if ($result->isEditable())
                <a href="{{ route('service-results.edit', $result->id) }}" class="btn btn-outline-secondary" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                
                @if ($result->status === 'draft')
                  <form action="{{ route('service-results.submit-approval', $result->id) }}" method="POST" style="display: inline;" class="submit-approval-form">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning" title="Submit for Approval">
                      <i class="fas fa-paper-plane"></i>
                    </button>
                  </form>
                @endif
              @endif
              
              @if ($result->status === 'pending_approval')
                <button type="button" class="btn btn-outline-success" title="Approve" onclick="approveResult({{ $result->id }}, 'approve')">
                  <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn btn-outline-danger" title="Reject" onclick="approveResult({{ $result->id }}, 'reject')">
                  <i class="fas fa-times"></i>
                </button>
              @endif
              
              @if ($result->isEditable())
                <form action="{{ route('service-results.destroy', $result->id) }}" method="POST" style="display: inline;" class="delete-result-form">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this result?')">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="text-center py-5">
        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No Results Found</h4>
        <p class="text-muted">Get started by adding your first service result.</p>
        <a href="{{ route('service-results.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Add First Result
        </a>
      </div>
    </div>
  @endforelse
</div>
