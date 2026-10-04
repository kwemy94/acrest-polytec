<div class="galerie" role="list">
    @foreach ($photos as $photo)
        <figure role="listitem">
            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-photo" data-photo="{{ asset($photo['image']) }}" data-legende="{{ $photo['legende'] }}" aria-label="Agrandir : {{ $photo['legende'] }}">
                <img src="{{ asset($photo['image']) }}" alt="{{ $photo['legende'] }}" loading="lazy">
            </button>
            <figcaption>{{ $photo['legende'] }}</figcaption>
        </figure>
    @endforeach
</div>
