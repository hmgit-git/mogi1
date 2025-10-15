<div class="item-list">
    @forelse ($items as $item)
    <a href="{{ route('items.show', $item->id) }}" class="item-link">
        <div class="item-card">
            <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
            <p>{{ $item->name }}</p>

            @if ($item->is_sold)
            <span class="sold-badge">sold</span>
            @endif
        </div>
    </a>
    @empty
    <p>商品がありません。</p>
    @endforelse
</div>