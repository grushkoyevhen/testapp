@if ($paginator->hasPages())
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            @if(!$paginator->onFirstPage())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            @endif

            @foreach($elements as $element)
                @if(is_string($element))
                    <li class="page-item"><a class="page-link" href="#">{{ $element }}</a></li>
                @endif

                @if(is_array($element))
                    @foreach($element as $page=>$url)
                        @if($paginator->currentPage() == $page)
                            <li class="page-item active"><a class="page-link" href="{{ route('post.list.page', ['id' => $page]) }}">{{ $page }}</a></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ route('post.list.page', ['id' => $page]) }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if(!$paginator->onFirstPage())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif
