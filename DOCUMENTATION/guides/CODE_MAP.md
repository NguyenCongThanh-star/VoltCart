# Bản đồ source code VoltCart

Đọc file này để biết **muốn tìm hoặc sửa cái gì thì vào đâu**. Không cần biết code vẫn đọc được.

## 1. Một trang web được tạo ra như thế nào

Khi khách mở một địa chỉ, ví dụ `/products`, yêu cầu đi qua 4 chặng:

| Chặng | Nằm ở đâu | Làm gì | Ví dụ đời thường |
|---|---|---|---|
| 1. Route | `CODE/routes/` | Xem địa chỉ và chọn người xử lý | Lễ tân chỉ khách tới đúng quầy |
| 2. Controller | `CODE/app/Http/Controllers/` | Nhận yêu cầu, lấy dữ liệu, chọn trang trả về | Nhân viên quầy |
| 3. Model / Service | `CODE/app/Models/`, `CODE/app/Services/` | Đọc, ghi database và áp dụng quy tắc nghiệp vụ | Kho hàng và sổ quy định |
| 4. View | `CODE/resources/views/` | Dựng trang HTML để hiển thị | Tờ giấy đưa lại cho khách |

## 2. Ba khu vực

Code được chia theo ba khu vực. Tên thư mục giống nhau ở controller, view và route, nên biết khu vực là biết chỗ tìm.

| Khu vực | Dành cho | Tính năng | Controller | View | Route |
|---|---|---|---|---|---|
| **Shop** | Khách vãng lai, thành viên | Trang chủ, sản phẩm, tìm kiếm, giỏ hàng, checkout, chat | `Controllers/Shop/` | `views/shop/` | `routes/shop.php` |
| **Account** | Thành viên | Đăng ký, đăng nhập, hồ sơ, lịch sử đơn | `Controllers/Account/` | `views/account/` | `routes/account.php` |
| **Admin** | Quản trị viên | Sản phẩm, đơn hàng, khách hàng, mã giảm giá, báo cáo | `Controllers/Admin/` | `views/admin/` | `routes/admin.php` |

## 3. Từng thư mục trong `CODE/`

### Thư mục nhóm làm việc hằng ngày

| Thư mục / file | Chứa gì |
|---|---|
| `routes/web.php` | Chỉ gọi 3 file route bên dưới. Không thêm đường dẫn vào đây. |
| `routes/shop.php`, `account.php`, `admin.php` | Danh sách địa chỉ của từng khu vực. Đầu mỗi file có bảng các địa chỉ dự kiến. |
| `app/Http/Controllers/Shop/`, `Account/`, `Admin/` | Controller của từng khu vực. |
| `app/Http/Middleware/` | Bộ lọc chạy trước controller. `EnsureUserIsAdmin` chặn người thường vào `/admin`. |
| `app/Models/` | Mỗi file là một bảng trong database: `User`, `Category`, `Product`, `ProductImage`. |
| `app/Services/` | Quy tắc nghiệp vụ dùng chung (xem mục 4). |
| `resources/views/layouts/` | Khung trang dùng chung: header, footer (khách) và sidebar (admin). |
| `resources/views/components/` | Mảnh giao diện dùng lại nhiều nơi: thẻ sản phẩm, hộp thông báo, phân trang. |
| `resources/views/shop/`, `account/`, `admin/` | Các trang của từng khu vực. |
| `public/css/`, `public/js/`, `public/images/` | CSS, JavaScript và ảnh tự viết. Trình duyệt tải trực tiếp từ đây. |
| `database/migrations/` | Mỗi file tạo một bảng. Chạy bằng `php artisan migrate`. |
| `database/seeders/` | Code nạp dữ liệu mẫu. |

### Thư mục của khung Laravel, hiếm khi sửa

| Thư mục / file | Chứa gì |
|---|---|
| `config/` | Cấu hình: tên ứng dụng, database, session, mail… |
| `bootstrap/app.php` | Khởi động ứng dụng, nơi đăng ký middleware. |
| `storage/` | File do ứng dụng sinh ra: log lỗi, ảnh upload. Không sửa tay. |
| `tests/`, `phpunit.xml` | Test tự động. |
| `vendor/` | Thư viện tải về bằng `composer install`. Không sửa, không đưa lên GitHub. |
| `composer.json`, `composer.lock` | Danh sách thư viện và phiên bản chính xác. |
| `artisan` | Công cụ dòng lệnh của Laravel. |
| `.env` | Cấu hình riêng từng máy (mật khẩu database). Không đưa lên GitHub. |
| `.env.example` | Bản mẫu của `.env`. |

## 4. Quy tắc nghiệp vụ nằm ở đâu

Mọi quy tắc trong [bản phân tích nghiệp vụ](../analysis/PHAN_TICH_NGHIEP_VU.md) (mã R-01 đến R-30) được viết **một lần** trong `app/Services/`. Controller chỉ gọi, không tự tính lại.

| Service | Lo việc gì | Quy tắc |
|---|---|---|
| `PricingService` | Khách trả giá nào: giá khách hay giá thành viên | R-02, R-03 |
| `CartService` | Thêm, sửa, xóa trong giỏ; kiểm tra tồn kho | R-05, R-06 |
| `CouponService` | Kiểm tra và áp mã giảm giá | R-11 đến R-13 |
| `OrderService` | Tạo đơn, trừ và cộng kho, đổi trạng thái đơn | R-04, R-07 đến R-10, R-14 đến R-19 |

Hiện mới có `PricingService` chạy được. Ba service còn lại là khung, ghi sẵn quy tắc cần làm.

## 5. Giá trị cố định

Các giá trị như quyền người dùng hay trạng thái sản phẩm được khai báo thành hằng số trong Model. Luôn dùng hằng số, không gõ tay chữ.

| Thay vì gõ | Hãy dùng |
|---|---|
| `'admin'` | `User::ROLE_ADMIN` |
| `'locked'` | `User::STATUS_LOCKED` |
| `'hidden'` | `Product::STATUS_HIDDEN` |

Gõ sai tên hằng số thì PHP báo lỗi ngay. Gõ sai chữ thì chương trình vẫn chạy nhưng cho kết quả sai.

## 6. Tra nhanh: muốn làm X thì sửa ở đâu

| Muốn… | Sửa ở |
|---|---|
| Đổi màu, font toàn trang | `public/css/` |
| Sửa header, footer | `resources/views/layouts/` |
| Thêm một trang mới | Xem [HOW_TO_ADD_FEATURE.md](HOW_TO_ADD_FEATURE.md) |
| Thêm một cột vào bảng | Tạo migration mới trong `database/migrations/` |
| Đổi cách tính giá | `app/Services/PricingService.php` |
| Đổi phí giao hàng | `app/Services/OrderService.php` |
| Thêm sản phẩm mẫu | `database/seeders/` và `DATA/sample-data/` |
| Đổi mật khẩu database trên máy mình | `CODE/.env` |
