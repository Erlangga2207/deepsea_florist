{{-- Daftar menu berkelompok. Dipakai sidebar desktop dan offcanvas HP, sumbernya $menuAdmin (config/admin-menu.php). --}}
@foreach ($menuAdmin as $grup)
    <div @class(['admin-grup', 'admin-grup-bawah' => $grup['bawah'] ?? false])>
        @if ($grup['judul'])
            <div class="admin-grup-judul">{{ $grup['judul'] }}</div>
        @endif
        <ul class="admin-menu">
            @foreach ($grup['item'] as $i)
                @php
                    $pola = Str::endsWith($i['route'], ['.index', '.edit']) ? Str::beforeLast($i['route'], '.').'.*' : $i['route'];
                    $aktif = request()->routeIs($pola);
                @endphp
                <li>
                    <a href="{{ route($i['route']) }}" @class(['aktif' => $aktif]) @if ($aktif) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $i['ikon'] }}"/></svg>
                        <span class="flex-grow-1">{{ $i['label'] }}</span>
                        @if ($i['jumlah'] > 0)
                            <span class="admin-badge">{{ $i['jumlah'] }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endforeach
