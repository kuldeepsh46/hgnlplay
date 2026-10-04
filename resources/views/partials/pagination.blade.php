{{-- Self-styled pagination (the site has no Tailwind/Bootstrap, so Laravel's default view renders huge arrows) --}}
@if ($paginator->hasPages())
<style>
.pgn { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 16px; font-size: 13px; color: #a9b9c7; }
.pgn ul { display: flex; flex-wrap: wrap; gap: 4px; list-style: none; margin: 0; padding: 0; }
.pgn li a, .pgn li span { display: inline-block; min-width: 34px; padding: 7px 10px; border-radius: 6px; text-align: center; background: #141c22; border: 1px solid #1f2832; color: #d4dee8; text-decoration: none; line-height: 1; }
.pgn li a:hover { border-color: var(--accent, #3f7871); color: #fff; }
.pgn li.on span { background: var(--accent, #3f7871); border-color: var(--accent, #3f7871); color: #fff; font-weight: 600; }
.pgn li.off span { opacity: .4; }
</style>
<nav class="pgn" aria-label="Pagination">
    <div>Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ number_format($paginator->total()) }}</div>
    <ul>
        @if ($paginator->onFirstPage())
            <li class="off"><span>‹ Prev</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Prev</a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="off"><span>{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="on"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next ›</a></li>
        @else
            <li class="off"><span>Next ›</span></li>
        @endif
    </ul>
</nav>
@endif
