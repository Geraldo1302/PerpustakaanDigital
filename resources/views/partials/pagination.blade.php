@if ($paginator->hasPages())
    <div class="rb-pager" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="btn btn-secondary btn-sm is-disabled">&larr; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secondary btn-sm">&larr; Sebelumnya</a>
        @endif

        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secondary btn-sm">Berikutnya &rarr;</a>
        @else
            <span class="btn btn-secondary btn-sm is-disabled">Berikutnya &rarr;</span>
        @endif
    </div>
@endif
