@props(['item'])

<span class="managed-menu-item">
    <a href="{{ $item['url'] }}" @if($item['new_tab']) target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
    @if(!empty($item['children']))
        <span class="managed-menu-item__children">
            @foreach($item['children'] as $child)
                <x-managed-menu-item :item="$child" />
            @endforeach
        </span>
    @endif
</span>
