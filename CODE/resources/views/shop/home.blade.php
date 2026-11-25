@extends('layouts.app')

@section('title', 'VoltCart - Technology for Everyone')

@section('content')
    <section class="site-container banner">
        <div class="banner-content">
            <p class="banner-label">VOLTCART TECHNOLOGY STORE</p>
            <h1>Nâng cấp trải nghiệm công nghệ của bạn</h1>
            <p>
                Khám phá điện thoại, laptop và phụ kiện phù hợp với nhu cầu học tập và làm việc.
                Tìm sản phẩm theo thương hiệu, mức giá và cấu hình bạn cần.
            </p>
            <div class="hero-actions">
                <a href="{{ route('products.index') }}" class="banner-button">Khám phá sản phẩm</a>
                <span>Mua hàng không cần đăng ký tài khoản</span>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="hero-glow"></div>
            <div class="laptop-illustration">
                <div class="laptop-display">
                    <div class="display-topline"><span>VOLTCART</span><span>● ● ●</span></div>
                    <div class="display-content">
                        <p>TECHNOLOGY FOR EVERYONE</p>
                        <h2>Find the right device.</h2>
                        <span>Smartphones · Laptops · Accessories</span>
                    </div>
                </div>
                <div class="laptop-base"></div>
            </div>
            <div class="hero-note">
                <span class="note-dot"></span>
                <div><strong>Member Price</strong><p>Ưu đãi dành cho thành viên</p></div>
            </div>
        </div>
    </section>

    <section class="site-container category-section" aria-labelledby="category-title">
        <div class="section-heading">
            <div>
                <p class="brand-subtitle">SHOP BY CATEGORY</p>
                <h2 class="section-title" id="category-title">Danh mục sản phẩm</h2>
            </div>
            <a href="{{ route('products.index') }}">Xem tất cả →</a>
        </div>

        <div class="category-grid">
            <article class="category-card">
                <span class="category-icon" aria-hidden="true">📱</span>
                <h3>Smartphones</h3>
                <p>Điện thoại cho học tập, công việc và giải trí.</p>
                <a href="{{ route('products.index', ['category' => 'smartphones']) }}">Xem điện thoại →</a>
            </article>
            <article class="category-card">
                <span class="category-icon" aria-hidden="true">💻</span>
                <h3>Laptops</h3>
                <p>Laptop phù hợp cho lập trình, học tập và làm việc.</p>
                <a href="{{ route('products.index', ['category' => 'laptops']) }}">Xem laptop →</a>
            </article>
            <article class="category-card">
                <span class="category-icon" aria-hidden="true">🎧</span>
                <h3>Accessories</h3>
                <p>Phụ kiện công nghệ cho nhu cầu hằng ngày.</p>
                <a href="{{ route('products.index', ['category' => 'accessories']) }}">Xem phụ kiện →</a>
            </article>
        </div>
    </section>

    <section class="site-container product-section" aria-labelledby="featured-title">
        <div class="section-heading">
            <div>
                <p class="brand-subtitle">FEATURED PRODUCTS</p>
                <h2 class="section-title" id="featured-title">Sản phẩm nổi bật</h2>
            </div>
            <a href="{{ route('products.index') }}">Xem tất cả →</a>
        </div>

        <div class="product-grid">
            <article class="product-card">
                <div class="product-image">
                    <span class="product-label">Nổi bật</span>
                    <img src="{{ asset('images/products/phone-placeholder.svg') }}" alt="Hình minh họa điện thoại Samsung Galaxy" width="320" height="220" loading="lazy">
                </div>
                <div class="product-info">
                    <p class="product-category">Smartphone</p>
                    <h3>Samsung Galaxy</h3>
                    <p class="product-spec">RAM: 8GB | Bộ nhớ: 256GB</p>
                    <div class="product-prices">
                        <p class="guest-price">Giá khách: 15.990.000 ₫</p>
                        <p class="member-price">Giá thành viên: 15.490.000 ₫</p>
                    </div>
                    <a class="product-detail-link" href="{{ route('products.index') }}">Xem sản phẩm</a>
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
                    <div class="product-prices">
                        <p class="guest-price">Giá khách: 19.990.000 ₫</p>
                        <p class="member-price">Giá thành viên: 19.490.000 ₫</p>
                    </div>
                    <a class="product-detail-link" href="{{ route('products.index') }}">Xem sản phẩm</a>
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
                    <div class="product-prices">
                        <p class="guest-price">Giá khách: 990.000 ₫</p>
                        <p class="member-price">Giá thành viên: 890.000 ₫</p>
                    </div>
                    <a class="product-detail-link" href="{{ route('products.index') }}">Xem sản phẩm</a>
                </div>
            </article>
        </div>
    </section>

    <section class="site-container benefit-section" aria-label="Lợi ích mua sắm">
        <article class="benefit-card">
            <span aria-hidden="true">🚚</span>
            <h3>Giao hàng thuận tiện</h3>
            <p>Thông tin giao hàng rõ ràng.</p>
        </article>
        <article class="benefit-card">
            <span aria-hidden="true">🔒</span>
            <h3>Mua sắm an tâm</h3>
            <p>Thông tin sản phẩm minh bạch.</p>
        </article>
        <article class="benefit-card">
            <span aria-hidden="true">💬</span>
            <h3>Hỗ trợ khách hàng</h3>
            <p>Luôn sẵn sàng hỗ trợ người mua.</p>
        </article>
    </section>
@endsection
