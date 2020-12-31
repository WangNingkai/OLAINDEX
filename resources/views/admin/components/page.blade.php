@if ($paginator->hasPages())
    <ul class="pagination m-0 ms-auto" role="navigation">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link" tabindex="-1" aria-disabled="true">
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"--}}
                    {{--                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"--}}
                    {{--                         stroke-linecap="round" stroke-linejoin="round">--}}
                    {{--                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>--}}
                    {{--                        <polyline points="15 6 9 12 15 18"></polyline>--}}
                    {{--                    </svg>--}}
                    @lang('pagination.previous')
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}"
                   rel="prev">@lang('pagination.previous')</a>
            </li>
        @endif

        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    @lang('pagination.next')
                    {{--                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"--}}
                    {{--                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"--}}
                    {{--                         stroke-linecap="round" stroke-linejoin="round">--}}
                    {{--                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>--}}
                    {{--                        <polyline points="9 6 15 12 9 18"></polyline>--}}
                    {{--                    </svg>--}}
                </a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">
                    @lang('pagination.next')
                    {{--                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"--}}
                    {{--                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"--}}
                    {{--                         stroke-linecap="round" stroke-linejoin="round">--}}
                    {{--                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>--}}
                    {{--                        <polyline points="9 6 15 12 9 18"></polyline>--}}
                    {{--                    </svg>--}}
                </span>
            </li>
        @endif
    </ul>
@endif
