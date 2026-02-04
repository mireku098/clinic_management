<!-- Services Grid -->
<div class="row">
  @forelse ($services as $service)
    <div class="col-lg-4 col-md-6 mb-4">
      <div class="card h-100 service-card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="service-icon">
              <i class="fas fa-user-md"></i>
            </div>
            <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'warning' }}">
              {{ ucfirst($service->status) }}
            </span>
          </div>
          <h5 class="card-title">{{ $service->service_name }}</h5>
          <p class="card-text text-muted">
            {{ $service->description ?? 'No description available' }}
          </p>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">{{ $service->service_code }}</small>
            <small class="text-muted">{{ ucfirst($service->category) }}</small>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="h5 text-primary mb-0">GH₵{{ number_format($service->price, 2) }}</span>
            <div class="btn-group btn-group-sm">
              <a href="{{ route('services.edit', $service->id) }}" class="btn btn-outline-primary" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display: inline;" class="status-toggle-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-{{ $service->status === 'active' ? 'danger' : 'success' }}" 
                        title="{{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}"
                        onclick="return confirm('Are you sure you want to {{ $service->status === 'active' ? 'deactivate' : 'activate' }} this service?')">
                  <i class="fas fa-{{ $service->status === 'active' ? 'pause' : 'play' }}"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="text-center py-5">
        <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No Services Found</h4>
        <p class="text-muted">Get started by adding your first service.</p>
        <a href="{{ route('services.add') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Add First Service
        </a>
      </div>
    </div>
  @endforelse
</div>
