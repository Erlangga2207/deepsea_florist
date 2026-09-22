@props(['warna' => 'neutral', 'label'])

<span {{ $attributes->class(['pill', 'pill-'.$warna]) }}>{{ $label }}</span>
