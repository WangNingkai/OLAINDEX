<div class="text-center">
    <a href="{{ $file['download'] }}" data-fancybox="image-list">
        <img src="{{ $file['thumb'] ?? $file['download']  }}" alt="{{ $file['name'] }}" class="img-fluid">
    </a>
</div>
