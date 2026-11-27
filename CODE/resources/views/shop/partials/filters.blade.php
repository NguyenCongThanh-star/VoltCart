@php
    $selectedCategories = (array) request()->input('category', []);
    $selectedBrands = (array) request()->input('brand', []);
    $selectedRam = (array) request()->input('ram', []);
@endphp

<form
    id="{{ $formId }}"
    class="filter-form"
    action="{{ route('products.index') }}"
    method="GET"
>
    {{-- Giữ lại từ khóa tìm kiếm hiện tại --}}
    @if (request()->filled('q'))
        <input type="hidden" name="q" value="{{ request('q') }}">
    @endif

    {{-- Giữ lại cách sắp xếp trên form mobile --}}
    @if ($carrySort)
        <input
            type="hidden"
            name="sort"
            value="{{ request('sort', 'newest') }}"
        >
    @endif

    {{-- CATEGORY --}}
    <fieldset class="filter-group">
        <legend>Danh mục</legend>

        <label class="filter-option" for="{{ $formId }}-smartphones">
            <input
                type="checkbox"
                id="{{ $formId }}-smartphones"
                name="category[]"
                value="smartphones"
                @checked(in_array('smartphones', $selectedCategories, true))
            >
            Điện thoại
        </label>

        <label class="filter-option" for="{{ $formId }}-laptops">
            <input
                type="checkbox"
                id="{{ $formId }}-laptops"
                name="category[]"
                value="laptops"
                @checked(in_array('laptops', $selectedCategories, true))
            >
            Laptop
        </label>

        <label class="filter-option" for="{{ $formId }}-accessories">
            <input
                type="checkbox"
                id="{{ $formId }}-accessories"
                name="category[]"
                value="accessories"
                @checked(in_array('accessories', $selectedCategories, true))
            >
            Phụ kiện
        </label>
    </fieldset>

    {{-- BRAND --}}
    <fieldset class="filter-group">
        <legend>Thương hiệu</legend>

        <label class="filter-option" for="{{ $formId }}-brand-a">
            <input
                type="checkbox"
                id="{{ $formId }}-brand-a"
                name="brand[]"
                value="brand-a"
                @checked(in_array('brand-a', $selectedBrands, true))
            >
            Brand A
        </label>

        <label class="filter-option" for="{{ $formId }}-brand-b">
            <input
                type="checkbox"
                id="{{ $formId }}-brand-b"
                name="brand[]"
                value="brand-b"
                @checked(in_array('brand-b', $selectedBrands, true))
            >
            Brand B
        </label>
    </fieldset>

    {{-- PRICE RANGE --}}
    <fieldset class="filter-group">
        <legend>Khoảng giá (VNĐ)</legend>

        <div class="price-range-fields">
            <div>
                <label
                    class="form-label"
                    for="{{ $formId }}-min-price"
                >
                    Từ
                </label>

                <input
                    type="number"
                    id="{{ $formId }}-min-price"
                    name="min_price"
                    min="0"
                    step="1000"
                    placeholder="Giá thấp"
                    value="{{ request('min_price') }}"
                >
            </div>

            <div>
                <label
                    class="form-label"
                    for="{{ $formId }}-max-price"
                >
                    Đến
                </label>

                <input
                    type="number"
                    id="{{ $formId }}-max-price"
                    name="max_price"
                    min="0"
                    step="1000"
                    placeholder="Giá cao"
                    value="{{ request('max_price') }}"
                >
            </div>
        </div>
    </fieldset>

    {{-- RAM --}}
    <fieldset class="filter-group">
        <legend>Dung lượng RAM</legend>

        <label class="filter-option" for="{{ $formId }}-ram-8">
            <input
                type="checkbox"
                id="{{ $formId }}-ram-8"
                name="ram[]"
                value="8"
                @checked(in_array('8', $selectedRam, true))
            >
            8GB
        </label>

        <label class="filter-option" for="{{ $formId }}-ram-16">
            <input
                type="checkbox"
                id="{{ $formId }}-ram-16"
                name="ram[]"
                value="16"
                @checked(in_array('16', $selectedRam, true))
            >
            16GB
        </label>
    </fieldset>

    {{-- BUTTONS --}}
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-block">
            Áp dụng bộ lọc
        </button>

        <a
            href="{{ route('products.index') }}"
            class="btn btn-outline btn-block"
        >
            Xóa bộ lọc
        </a>
    </div>
</form>
