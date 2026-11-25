# Cách thêm một tính năng mới

Mọi tính năng đều đi theo cùng 5 bước. Ví dụ dưới đây thêm trang **danh sách sản phẩm** (`/products`, mã C-02) vào khu vực Shop.

Trước khi bắt đầu, xác định tính năng thuộc khu vực nào: **Shop**, **Account** hay **Admin**
(xem [CODE_MAP.md](CODE_MAP.md)). Mọi file bạn tạo sẽ nằm trong thư mục của khu vực đó.

## Bước 0 – Tạo nhánh

```bash
git checkout develop
git pull
git checkout -b feature/product-list
```

## Bước 1 – Thêm đường dẫn

Mở `CODE/routes/shop.php` và thêm:

```php
use App\Http\Controllers\Shop\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
```

Dòng này nghĩa là: khi có người mở `/products`, hãy gọi hàm `index` của `ProductController`.

## Bước 2 – Tạo controller

Chạy trong thư mục `CODE`:

```bash
php artisan make:controller Shop/ProductController
```

Mở file vừa tạo trong `app/Http/Controllers/Shop/` và viết hàm `index`:

```php
public function index()
{
    $products = Product::visible()->latest()->paginate(12);

    return view('shop.products.index', ['products' => $products]);
}
```

Controller chỉ làm 3 việc: nhận yêu cầu, lấy dữ liệu, trả về trang.
Phần tính toán theo quy tắc nghiệp vụ (giá, tồn kho, mã giảm giá) thì gọi service trong `app/Services/`.

## Bước 3 – Tạo trang hiển thị

Tạo file `CODE/resources/views/shop/products/index.blade.php`:

```blade
@foreach ($products as $product)
    <h2>{{ $product->name }}</h2>
@endforeach

{{ $products->links() }}
```

Tên `shop.products.index` ở bước 2 chính là đường dẫn file này, thay dấu `/` bằng dấu chấm.

## Bước 4 – Cần bảng mới hoặc cột mới thì tạo migration

Ví dụ này không cần. Khi cần, **tạo migration mới**, không sửa migration cũ đã đưa lên GitHub:

```bash
php artisan make:migration add_warranty_months_to_products_table
```

Sau đó báo nhóm chạy `php artisan migrate`.

## Bước 5 – Kiểm tra rồi mới gửi

- [ ] Mở trang trên trình duyệt, thử cả khi chưa đăng nhập và đã đăng nhập
- [ ] Thử nhập sai, để trống, không có dữ liệu: trang phải hiện thông báo rõ ràng
- [ ] Thu nhỏ cửa sổ xuống cỡ điện thoại
- [ ] Thêm test case tương ứng vào `DOCUMENTATION/TEST_CASES.xlsx`
- [ ] Commit, đẩy nhánh lên và mở pull request vào `develop`

```bash
git add .
git commit -m "Add product list page"
git push -u origin feature/product-list
```

## Ba điều dễ sai

| Đừng | Hãy |
|---|---|
| Tự tính giá trong controller hoặc view | Gọi `PricingService` |
| Gõ tay chữ `'admin'`, `'hidden'` | Dùng hằng số: `User::ROLE_ADMIN`, `Product::STATUS_HIDDEN` |
| Thêm đường dẫn vào `routes/web.php` | Thêm vào file của khu vực: `shop.php`, `account.php`, `admin.php` |

## Lệnh hay dùng

Chạy trong thư mục `CODE`:

| Lệnh | Tác dụng |
|---|---|
| `php artisan serve` | Chạy trang web tại http://127.0.0.1:8000 |
| `php artisan route:list` | Xem mọi đường dẫn đang có |
| `php artisan migrate` | Tạo các bảng mới |
| `php artisan migrate:fresh --seed` | Xóa hết, tạo lại bảng và nạp dữ liệu mẫu |
| `php artisan make:controller Admin/TênController` | Tạo controller |
| `php artisan make:model Tên -m` | Tạo model kèm migration |
