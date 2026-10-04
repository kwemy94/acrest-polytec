@foreach (['succes' => 'success', 'info' => 'info', 'erreur' => 'danger'] as $cle => $type)
    @if (session($cle))
        <div class="alert alert-{{ $type }} d-flex gap-2 align-items-start" role="status">
            <i class="bi {{ $type === 'success' ? 'bi-check-circle-fill' : ($type === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill') }} mt-1"></i>
            <div>{{ session($cle) }}</div>
        </div>
    @endif
@endforeach
