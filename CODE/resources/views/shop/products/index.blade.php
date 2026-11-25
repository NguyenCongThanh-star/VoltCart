@extends('layouts.app')

@section('title', 'Sản phẩm - VoltCart')

@section('content')
    <section class="site-container page-intro">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span aria-hidden="true">/</span>
            <span>Sản phẩm</span>
        </nav>
        <h1>Sản phẩm công nghệ</h1>
        <p>Tìm thiết bị phù hợp với ngân sách và nhu cầu của bạn.</p>
    </section>

    <div class="site-container listing-layout">
        <aside class="filter-panel desktop-filter" aria-labelledby="desktop-filter-title">
            <h2 id="desktop-filter-title">Bộ lọc sản phẩm</h2>
            @include('shop.partials.filters', ['formId' => 'filter-form-desktop', 'carrySort' => false])
        </aside>

        <section class="listing-results" aria-label="Danh sách sản phẩm">
            <details class="mobile-filter">
                <summary>☷ Bộ lọc sản phẩm</summary>
                <div class="filter-inner">
                    @include('shop.partials.filters', ['formId' => 'filter-form-mobile', 'carrySort' => true])
                </div>
            </details>

            <div class="listing-toolbar">
                <p class="listing-count"><strong>3</strong> sản phẩm</p>
                {{-- The select is attached to the desktop filter form with form="...",
                     so changing the sort also sends the current search and filters. --}}
                <label class="sort-control" for="sort-by">
                    Sắp xếp:
                    <select id="sort-by" name="sort" form="filter-form-desktop" onchange="this.form.submit()">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                        <option value="price-asc" @selected(request('sort') === 'price-asc')>Giá thấp đến cao</option>
                        <option value="price-desc" @selected(request('sort') === 'price-desc')>Giá cao đến thấp</option>
                    </select>
                </label>
            </div>

            <div class="listing-grid">
                <article class="product-card">
                    <div class="product-image">
                        <span class="product-label">Nổi bật</span>
                        <img src="{{ asset('images/products/phone-placeholder.svg') }}" alt="Hình minh họa điện thoại Samsung Galaxy" width="320" height="220" loading="lazy">
                    </div>
                    <div class="product-info">
                        <p class="product-category">Smartphone</p>
                        <h3>Samsung Galaxy</h3>
                        <p class="product-spec">RAM: 8GB | Bộ nhớ: 256GB</p>
                        <span class="stock-status">Tình trạng kho: dữ liệu mẫu</span>
                        <div class="product-prices">
                            <p class="guest-price">Giá khách: 15.990.000 ₫</p>
                            <p class="member-price">Giá thành viên: 15.490.000 ₫</p>
                        </div>
                        <a class="product-detail-link" href="#">Xem chi tiết</a>
                    </div>
                </article>

                <article class="product-card">
                    <div class="product-image">
                        <span class="product-label">Bán chạy</span>
                        <img src="{{ asset('images/products/laptop-placeholder.svg') }}" alt="Hình minh họa laptop ASUS Vivobook" width="320" height="220" loading="lazy">
                    </div>
                    <div class="product-info">
                        <p class="product-category">Laptop</p>
                        <h3>ASUS Vivobook</h3>
                        <p class="product-spec">RAM: 16GB | Bộ nhớ: 512GB</p>
                        <span class="stock-status">Tình trạng kho: dữ liệu mẫu</span>
                        <div class="product-prices">
                            <p class="guest-price">Giá khách: 19.990.000 ₫</p>
                            <p class="member-price">Giá thành viên: 19.490.000 ₫</p>
                        </div>
                        <a class="product-detail-link" href="#">Xem chi tiết</a>
                    </div>
                </article>

                <article class="product-card">
                    <div class="product-image">
                        <span class="product-label">Mới</span>
                        <img src="{{ asset('images/products/earbuds-placeholder.svg') }}" alt="Hình minh họa tai nghe không dây" width="320" height="220" loading="lazy">
                    </div>
                    <div class="product-info">
                        <p class="product-category">Accessories</p>
                        <h3>Wireless Earbuds</h3>
                        <p class="product-spec">Bluetooth: 5.3 | Pin: 24 giờ</p>
                        <span class="stock-status">Tình trạng kho: dữ liệu mẫu</span>
                        <div class="product-prices">
                            <p class="guest-price">Giá khách: 990.000 ₫</p>
                            <p class="member-price">Giá thành viên: 890.000 ₫</p>
                        </div>
                        <a class="product-detail-link" href="#">Xem chi tiết</a>
                    </div>
                </article>
            </div>
        </section>
    </div>
@endsection
