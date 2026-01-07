@if ($paginator->hasPages())
<nav class="flex items-center justify-between" aria-label="Pagination Navigation">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <span class="px-3 py-2 mx-1 text-sm text-gray-500 border border-gray-300 rounded-lg cursor-not-allowed bg-gray-50">
            Previous
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 mx-1 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Previous
        </a>
    @endif

    <!-- Pagination Elements -->
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <span class="px-2 py-2 mx-1 text-sm text-gray-500">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="px-4 py-2 mx-1 text-sm font-bold text-white bg-brand-blue border border-brand-blue rounded-lg">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="px-4 py-2 mx-1 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    <!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 mx-1 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Next
        </a>
    @else
        <span class="px-3 py-2 mx-1 text-sm text-gray-500 border border-gray-300 rounded-lg cursor-not-allowed bg-gray-50">
            Next
        </span>
    @endif
</nav>
@endif
