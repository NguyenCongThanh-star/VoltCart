<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoltCart - cửa hàng điện thoại, laptop và phụ kiện công nghệ.">
    <title>@yield('title', 'VoltCart - Technology for Everyone')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div class="topbar">
        <div class="site-container topbar-content">
            <span>VoltCart - Technology for Everyone</span>
            <div>
                {{-- /login and /cart have no route yet (weeks 4-5), so url() is used here.
                     Switch to route('login') / route('cart.index') once Thanh adds them. --}}
                <a href="{{ url('/login') }}">Tài khoản</a>
                <a href="mailto:support@voltcart.com">Hỗ trợ: support@voltcart.com</a>
            </div>
        </div>
    </div>

    <header class="main-header">
        <div class="site-container main-header-content">
            <a class="brand" href="{{ route('home') }}" aria-label="VoltCart - Trang chủ">
                <span class="brand-icon" aria-hidden="true">⚡</span>
                <span>VoltCart</span>
            </a>

            <form class="search-form" action="{{ route('products.index') }}" method="GET" role="search">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Bạn cần tìm điện thoại, laptop...?"
                    aria-label="Tìm kiếm sản phẩm">
                <button type="submit">Tìm kiếm</button>
            </form>

            <div class="header-actions">
                <a href="{{ url('/login') }}">Đăng nhập</a>
                <a href="{{ url('/cart') }}">Giỏ hàng (<span id="cart-count">0</span>)</a>
            </div>
        </div>
    </header>

    <nav class="main-nav" aria-label="Danh mục chính">
        <div class="site-container nav-content">
            @php $activeCategory = request('category'); @endphp
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Trang chủ</a>
            <a href="{{ route('products.index', ['category' => 'smartphones']) }}" @class(['is-active' => $activeCategory === 'smartphones'])>Điện thoại</a>
            <a href="{{ route('products.index', ['category' => 'laptops']) }}" @class(['is-active' => $activeCategory === 'laptops'])>Laptop</a>
            <a href="{{ route('products.index', ['category' => 'accessories']) }}" @class(['is-active' => $activeCategory === 'accessories'])>Phụ kiện</a>
            <a href="{{ route('products.index') }}" @class(['is-active' => request()->routeIs('products.index') && ! $activeCategory])>Tất cả sản phẩm</a>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div>
                <h2>⚡ VoltCart</h2>
                <p>Technology for Everyone</p>
                <p>Công nghệ phù hợp cho học tập và công việc.</p>
            </div>
            <div>
                <h3>Về VoltCart</h3>
                <p><a href="{{ route('home') }}">Trang chủ</a></p>
                <p><a href="{{ route('products.index') }}">Sản phẩm</a></p>
            </div>
            <div>
                <h3>Hỗ trợ khách hàng</h3>
                <p>Hướng dẫn mua hàng</p>
                <p>Chính sách giao hàng</p>
            </div>
            <div>
                <h3>Liên hệ</h3>
                <p>support@voltcart.com</p>
                <p>Ho Chi Minh City, Vietnam</p>
            </div>
        </div>
        <div class="copyright">
            <p>Copyright © 2026 VoltCart. All Rights Reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
