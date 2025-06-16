@props(['data', 'label' => 'bản ghi'])
<div class="d-flex justify-content-between align-items-center">
    <!-- Dòng mô tả bên trái -->
    @if($data->firstItem() > 0 && $data->lastItem() > 0 && $data->total() > 0) <div>
        Hiện {{ $data->firstItem() }} đến {{ $data->lastItem() }} của {{ $data->total() }} {{ $label ?? 'dòng' }}
    </div>
    @endif
    <!-- Phân trang bên phải -->
    <div>
        @if ($data->lastPage() > 1)
        <ul class="pagination mb-0">
            <li class="page-item {{ ($data->currentPage() == 1) ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $data->url(1) }}" aria-label="First">Trang đầu</a>
            </li>

            @if ($data->currentPage() > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $data->url($data->currentPage() - 1) }}">
                    {{ $data->currentPage() - 1 }}
                </a>
            </li>
            @endif

            <li class="page-item active">
                <a class="page-link" href="#">{{ $data->currentPage() }}</a>
            </li>

            @if ($data->currentPage() < $data->lastPage())
                <li class="page-item">
                    <a class="page-link" href="{{ $data->url($data->currentPage() + 1) }}">
                        {{ $data->currentPage() + 1 }}
                    </a>
                </li>
                @endif

                <li class="page-item {{ ($data->currentPage() == $data->lastPage()) ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $data->url($data->lastPage()) }}" aria-label="Last">Trang cuối</a>
                </li>
        </ul>
        @endif
    </div>
</div>
