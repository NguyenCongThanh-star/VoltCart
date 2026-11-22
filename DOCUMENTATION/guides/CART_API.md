# API giỏ hàng, mã giảm giá và checkout

Tài liệu cho Mẫn (JS, giao diện) và Thành (backend). Định dạng chốt ngày 22/11/2026.
Code: `CODE/app/Http/Controllers/Shop/CartController.php`, `CODE/app/Services/CartService.php`.

## 1. Danh sách API

| Việc | Method + URL | Body gửi lên | Tên route |
|---|---|---|---|
| Lấy giỏ hiện tại | `GET /cart` (kèm `Accept: application/json`) | — | `cart.index` |
| Thêm vào giỏ (cộng dồn) | `POST /cart/items` | `{ "product_id": 12, "quantity": 1 }` | `cart.items.store` |
| Đổi số lượng | `PATCH /cart/items/{product_id}` | `{ "quantity": 3 }` | `cart.items.update` |
| Xóa một sản phẩm | `DELETE /cart/items/{product_id}` | — | `cart.items.destroy` |
| Áp mã giảm giá | `POST /cart/coupon` | `{ "code": "VOLT200K" }` | `cart.coupon.store` |
| Gỡ mã giảm giá | `DELETE /cart/coupon` | — | `cart.coupon.destroy` |

`GET /cart` mở bằng trình duyệt thì trả trang `shop/cart.blade.php`, gọi bằng `fetch` với `Accept: application/json` thì trả JSON.

**Header bắt buộc** cho mọi `fetch`:

```js
const res = await fetch('/cart/items', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({ product_id: 12, quantity: 1 }),
});
const data = await res.json();
```

Layout cần có `<meta name="csrf-token" content="{{ csrf_token() }}">` trong `<head>`.

## 2. Phản hồi thành công (HTTP 200)

```json
{
  "ok": true,
  "message": "Added to cart.",
  "cart": {
    "items": [
      {
        "product_id": 12,
        "name": "iPhone 15 128GB",
        "url": "http://127.0.0.1:8000/products/iphone-15-128gb",
        "image": "http://127.0.0.1:8000/storage/products/iphone-15.jpg",
        "unit_price": 19990000,
        "quantity": 1,
        "line_total": 19990000,
        "stock": 8,
        "problem": null
      }
    ],
    "count": 3,
    "subtotal": 21180000,
    "discount": 200000,
    "shipping_fee": 30000,
    "total": 21010000,
    "coupon": { "code": "VOLT200K", "discount_amount": 200000, "min_order_amount": 3000000 },
    "price_type": "member",
    "can_checkout": true
  },
  "notices": []
}
```

| Trường | Ý nghĩa |
|---|---|
| `count` | Tổng số lượng của mọi dòng, hiện trên icon giỏ |
| `subtotal` | Tổng thành tiền của các dòng **không có** `problem` |
| `discount` | Tiền giảm của mã, 0 nếu không có mã |
| `shipping_fee` | 30.000 khi có hàng để trả tiền, 0 khi giỏ trống (R-14) |
| `total` | `subtotal − discount + shipping_fee` (R-15) |
| `price_type` | `"member"` hoặc `"guest"`: đang tính giá thành viên hay giá khách |
| `can_checkout` | `true` khi có hàng và không dòng nào có `problem`; dùng để bật/tắt nút "Thanh toán" |
| `image` | `null` nếu sản phẩm chưa có ảnh |

**Giỏ trống:** `items` là `[]`, mọi số bằng 0, `coupon` là `null`, `can_checkout` là `false`.

## 3. Phản hồi lỗi (HTTP 422 hoặc 404)

```json
{
  "ok": false,
  "code": "NOT_ENOUGH_STOCK",
  "message": "Only 8 of iPhone 15 128GB left in stock. The maximum quantity is 8.",
  "errors": { "quantity": ["Only 8 of iPhone 15 128GB left in stock. The maximum quantity is 8."] },
  "cart": { "...": "giỏ hiện tại, không thay đổi" },
  "notices": []
}
```

JS nên dựa vào `code` để quyết định làm gì; câu chữ của `message` có thể thay đổi.

| HTTP | `code` | Khi nào | JS nên làm |
|---|---|---|---|
| 422 | `VALIDATION` | Thiếu trường, số lượng không phải số nguyên từ 1 đến 999 | Hiện lỗi theo `errors` |
| 422 | `NOT_ENOUGH_STOCK` | Số lượng vượt tồn kho (R-06) | Đưa ô số lượng về giá trị trong `cart`, hiện `message` |
| 422 | `PRODUCT_UNAVAILABLE` | Sản phẩm không tồn tại, bị ẩn hoặc hết hàng (R-05) | Hiện `message`, khóa nút thêm |
| 422 | `COUPON_INVALID` | Mã không tồn tại hoặc đã tắt (R-12) | Hiện lỗi dưới ô mã |
| 422 | `COUPON_MIN_NOT_MET` | Tạm tính chưa đủ mức tối thiểu; `message` nói còn thiếu bao nhiêu | Hiện lỗi dưới ô mã |
| 404 | `NOT_IN_CART` | Sửa/xóa sản phẩm không có trong giỏ | Vẽ lại giỏ từ `cart` |
| 419 | (Laravel) | Phiên hết hạn, token CSRF cũ | Báo "Phiên đã hết hạn" và tải lại trang |
| 500 | (Laravel) | Lỗi máy chủ | Báo lỗi chung, giữ nguyên giỏ |

Lỗi 419 và 500 do Laravel trả, **không** có `ok`, `code` hay `cart`.

## 4. Thông báo phụ: `notices`

Luôn có, thường là `[]`. Mỗi phần tử là `{ "code", "message" }`. Hiện mỗi notice thành một dòng thông báo riêng (ví dụ màu vàng).

| `code` | Khi nào |
|---|---|
| `COUPON_REMOVED` | Mã tự bị gỡ vì tạm tính tụt dưới mức tối thiểu hoặc mã đã bị tắt (R-13) |

## 5. Dòng có vấn đề: `problem`

| `problem` | Nghĩa | Gợi ý giao diện |
|---|---|---|
| `null` | Bình thường | — |
| `"not_enough_stock"` | `quantity` lớn hơn `stock` | Viền vàng, "Chỉ còn N" |
| `"out_of_stock"` | `stock` = 0 | Làm mờ dòng, "Hết hàng", nút xóa |
| `"unavailable"` | Admin đã ẩn sản phẩm | Làm mờ dòng, "Ngừng bán", nút xóa |

Dòng có `problem` không được cộng vào `subtotal`, và không checkout được cho tới khi khách sửa hoặc xóa dòng đó (R-07).

## 6. Quy ước

- Tên trường dạng `snake_case`.
- Tiền là số nguyên VND. JS tự định dạng, dùng chung một hàm trong `public/js/app.js`:
  ```js
  function formatVnd(n) { return new Intl.NumberFormat('vi-VN').format(n) + ' ₫'; }
  ```
- Không có giá trị thì là `null`, không bỏ trường.
- Thông báo bằng tiếng Anh cho toàn trang.

## 7. Trang (không phải JSON)

| Trang | View (Mẫn viết) | Biến nhận được |
|---|---|---|
| `GET /cart` | `shop/cart.blade.php` | `$cart`, `$notices` (giống JSON); `session('error')` khi checkout bị trả về |
| `GET /checkout` | `shop/checkout.blade.php` | `$cart`, `$notices`, `$prefill` (thông tin hồ sơ của thành viên) |
| `GET /checkout/confirmation` | `shop/order-confirmation.blade.php` | `$order` (kèm `items`), `$bankTransfer` |
| Mọi layout | `layouts/*.blade.php` | `$cartCount`: số trên icon giỏ khi tải trang |

**Form checkout** gửi `POST /checkout` (route `checkout.store`) với: `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `payment_method` (`cod` hoặc `bank_transfer`), kèm `@csrf`.
Ô nhập dùng `old('customer_name', $prefill['customer_name'])` để giữ dữ liệu khi có lỗi.

**Trang xác nhận:**
- Tạm tính, tổng: `$order->subtotal()`, `$order->total()`.
- Mỗi dòng: `$item->product_name`, `$item->unit_price`, `$item->quantity`, `$item->lineTotal()`.
- Khi `$order->payment_method === 'bank_transfer'`: hiện `$bankTransfer['bank_name']`, `account_number`, `account_name`, kèm nội dung chuyển khoản là `$order->code`. Thanh toán là giả lập, không có tiền thật.
