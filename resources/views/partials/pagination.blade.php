@if ($paginator->total() > 0)
    <nav class="pagination" aria-label="Navigasi halaman">
        <p class="muted small">Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ number_format($paginator->total(), 0, ',', '.') }} data</p>

        @if ($paginator->hasPages())
            <div class="pagination-links">
                @if ($paginator->onFirstPage())
                    <button type="button" class="btn btn-secondary" disabled>Sebelumnya</button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secondary">Sebelumnya</a>
                @endif

                <span class="btn btn-text muted" style="cursor:default">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secondary">Berikutnya</a>
                @else
                    <button type="button" class="btn btn-secondary" disabled>Berikutnya</button>
                @endif
            </div>
        @endif
    </nav>
@endif
