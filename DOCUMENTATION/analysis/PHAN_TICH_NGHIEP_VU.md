# VoltCart – Phân tích nghiệp vụ

> Bản nháp để cả nhóm đọc và góp ý. Các sơ đồ nằm trong thư mục [`diagrams/`](diagrams/).

## 1. Actor

| Mã | Actor | Là ai | Quyền |
|---|---|---|---|
| A1 | Khách vãng lai (Guest) | Người chưa đăng nhập | Xem, tìm, lọc sản phẩm; dùng giỏ hàng; đặt hàng với **giá khách**; đăng ký, đăng nhập |
| A2 | Thành viên (Member) | Khách đã đăng nhập | Mọi quyền của A1, cộng thêm: mua với **giá thành viên**, sửa hồ sơ, xem lịch sử đơn, chat với cửa hàng |
| A3 | Quản trị viên (Admin) | Người vận hành cửa hàng | Quản lý sản phẩm, danh mục, đơn hàng, khách hàng, mã giảm giá; trả lời chat; xem dashboard và báo cáo |

A1 và A2 là hai dạng của actor cha "Khách hàng". Không có actor bên ngoài: thanh toán là giả lập và hệ thống không gửi email.

## 2. Danh sách tính năng

### 2.1 Phía khách hàng (bắt buộc)

| Mã | Tính năng | Mô tả | Actor |
|---|---|---|---|
| C-01 | Trang chủ | Banner, sản phẩm nổi bật, menu danh mục | A1, A2 |
| C-02 | Danh sách sản phẩm | Xem theo danh mục (Điện thoại, Laptop, Phụ kiện), có phân trang | A1, A2 |
| C-03 | Tìm kiếm sản phẩm | Ô tìm trên header ở mọi trang; tìm theo tên sản phẩm và tên hãng; hiện số kết quả; báo khi không tìm thấy | A1, A2 |
| C-04 | Lọc và sắp xếp | Lọc theo hãng, khoảng giá, RAM; sắp xếp theo giá hoặc mới nhất; kết hợp được với từ khóa | A1, A2 |
| C-05 | Chi tiết sản phẩm | Ảnh, mô tả, thông số, giá khách và giá thành viên, tình trạng kho | A1, A2 |
| C-06 | Đăng ký | Tạo tài khoản, kiểm tra dữ liệu nhập | A1 |
| C-07 | Đăng nhập, đăng xuất | Tài khoản bị khóa thì không đăng nhập được | A1, A2 |
| C-08 | Hồ sơ cá nhân | Sửa họ tên, email, SĐT, địa chỉ giao hàng | A2 |
| C-09 | Giá thành viên | Thành viên luôn thấy và trả giá thành viên, nhất quán từ danh sách đến checkout | A2 |
| C-10 | Giỏ hàng | Thêm, đổi số lượng, xóa, xem tạm tính; không tải lại trang (AJAX) | A1, A2 |
| C-11 | Kiểm tra tồn kho | Không cho đặt vượt số lượng còn trong kho, kiểm tra lại lúc đặt hàng | A1, A2 |
| C-12 | Checkout | Form thông tin giao hàng; thành viên được điền sẵn từ hồ sơ; khách vãng lai không cần tài khoản | A1, A2 |
| C-13 | Mã giảm giá | Nhập mã, hệ thống kiểm tra và trừ tiền (AJAX) | A1, A2 |
| C-14 | Tóm tắt tiền | Tạm tính − Giảm giá + Phí giao hàng = Tổng | A1, A2 |
| C-15 | Thanh toán giả lập | Chọn COD hoặc chuyển khoản giả lập | A1, A2 |
| C-16 | Xác nhận đơn | Trang báo đặt hàng thành công, có mã đơn | A1, A2 |
| C-17 | Lịch sử đơn hàng | Danh sách đơn, chi tiết từng đơn, trạng thái do admin cập nhật | A2 |
| C-18 | Thông báo | Báo thành công, nhập sai, không có kết quả, lỗi hệ thống | A1, A2 |

### 2.2 Phía quản trị (bắt buộc)

| Mã | Tính năng | Mô tả |
|---|---|---|
| Q-01 | Đăng nhập admin, phân quyền | Khu vực `/admin` tách riêng; tài khoản thường không vào được |
| Q-02 | Dashboard | Tổng đơn, doanh thu, số khách, số sản phẩm; doanh thu theo ngày |
| Q-03 | Quản lý danh mục | Thêm, sửa, xóa danh mục |
| Q-04 | Quản lý sản phẩm | Thêm, sửa, xóa; ảnh, mô tả, giá khách, giá thành viên, tồn kho, trạng thái hiện/ẩn |
| Q-05 | Tìm sản phẩm trong admin | Tìm theo tên để sửa nhanh |
| Q-06 | Chặn xóa sai | Sản phẩm đã có trong đơn hàng thì không xóa được, gợi ý chuyển sang "Ẩn" |
| Q-07 | Tìm và xem đơn hàng | Tìm theo mã đơn, email, SĐT; lọc theo trạng thái; xem chi tiết |
| Q-08 | Cập nhật trạng thái đơn | Theo vòng đời ở mục 3.4; tồn kho tự động cộng lại khi hủy |
| Q-09 | Quản lý khách hàng | Xem thông tin, khóa hoặc mở tài khoản |
| Q-10 | Quản lý mã giảm giá | Tạo, sửa giá trị và đơn tối thiểu, bật/tắt |
| Q-11 | Báo cáo doanh thu | Chọn khoảng ngày, xem bảng tổng hợp |

### 2.3 Giao diện (bắt buộc)

| Mã | Yêu cầu |
|---|---|
| U-01 | Bố cục, màu sắc, font thống nhất giữa các trang |
| U-02 | Responsive trên máy tính và điện thoại |
| U-03 | Ít nhất một tương tác AJAX (đã có ở C-10 và C-13) |
| U-04 | Trạng thái đang tải, rỗng, lỗi ở những chỗ phù hợp |
| U-05 | Ảnh có kích thước hợp lý và có chữ thay thế (alt) |

### 2.4 Tính năng mở rộng

| Mã | Tính năng | Quyết định |
|---|---|---|
| M-13 | Chat giữa thành viên và cửa hàng (AJAX, tự cập nhật vài giây một lần) | **Làm** sau Checkpoint 02 – Mẫn phụ trách; cần giảng viên chấp thuận |
| M-01 | Tra cứu đơn cho khách vãng lai bằng mã đơn + email | **Làm** |
| M-02 | Khách tự hủy đơn khi còn Pending | Nếu còn thời gian |
| M-03 | Báo cáo sản phẩm sắp hết hàng | Nếu còn thời gian |
| M-04 → M-08 | Đổi mật khẩu, sản phẩm liên quan, sản phẩm bán chạy, gợi ý tìm kiếm AJAX, nhiều biểu đồ | Nếu còn thời gian |
| M-09 → M-12 | Đánh giá sao, so sánh 2 máy, quên mật khẩu qua email, CSV | Không làm |

### 2.5 Ngoài phạm vi

Biến thể sản phẩm, trả góp, thu cũ đổi mới, cổng thanh toán thật, quản lý IMEI/bảo hành, nhiều kho, nhiều cấp quyền admin, giao diện SPA, chat thời gian thực bằng WebSocket, gửi ảnh/file trong chat.

## 3. Quy tắc nghiệp vụ

### 3.1 Giá

| Mã | Quy tắc |
|---|---|
| R-01 | Mỗi sản phẩm có hai giá: giá khách và giá thành viên. Giá thành viên không được lớn hơn giá khách. |
| R-02 | Khách vãng lai luôn thấy và trả giá khách. Thành viên đã đăng nhập luôn thấy và trả giá thành viên. |
| R-03 | Giá được tính lại ở máy chủ tại mọi bước (danh sách, chi tiết, giỏ hàng, checkout). Không tin giá gửi lên từ trình duyệt. |
| R-04 | Khi đặt hàng, tên và đơn giá của từng sản phẩm được chép vào đơn. Admin đổi giá sau đó không làm thay đổi đơn cũ. |

### 3.2 Giỏ hàng và tồn kho

| Mã | Quy tắc |
|---|---|
| R-05 | Chỉ thêm được sản phẩm đang hiện và còn hàng. |
| R-06 | Số lượng mỗi sản phẩm trong giỏ: từ 1 đến số còn trong kho. |
| R-07 | Tồn kho được kiểm tra lần cuối khi bấm đặt hàng. Thiếu hàng thì báo lỗi và không tạo đơn. |
| R-08 | **Trừ kho** ngay khi tạo đơn thành công. |
| R-09 | **Cộng lại kho** khi đơn chuyển sang Cancelled. |
| R-10 | Tạo đơn, tạo các dòng sản phẩm và trừ kho xảy ra trong một giao dịch: hoặc thành công hết, hoặc không có gì thay đổi. |

### 3.3 Mã giảm giá và tổng tiền

| Mã | Quy tắc |
|---|---|
| R-11 | Mỗi đơn chỉ dùng một mã. |
| R-12 | Mã hợp lệ khi: tồn tại, đang bật, và tạm tính đạt mức đơn tối thiểu của mã. Ví dụ VOLT200K: giảm 200.000 ₫ cho đơn từ 3.000.000 ₫. |
| R-13 | Nếu khách bớt hàng làm tạm tính xuống dưới mức tối thiểu thì mã tự bị gỡ. |
| R-14 | Phí giao hàng cố định 30.000 ₫ cho mọi đơn. |
| R-15 | Tổng tiền = Tạm tính (đơn giá áp dụng × số lượng) − Giảm giá + Phí giao hàng. |

### 3.4 Đơn hàng

| Mã | Quy tắc |
|---|---|
| R-16 | Đơn mới luôn ở trạng thái Pending. |
| R-17 | Chỉ được chuyển theo thứ tự: Pending → Confirmed → Shipping → Completed. |
| R-18 | Admin hủy được đơn khi đang Pending hoặc Confirmed. Đơn đã Shipping thì không hủy. |
| R-19 | Completed và Cancelled là trạng thái cuối, không đổi được nữa. |
| R-20 | Đơn của khách vãng lai không gắn tài khoản; đơn của thành viên gắn với tài khoản và hiện trong lịch sử đơn. |
| R-21 | Doanh thu chỉ tính các đơn Completed. |

### 3.5 Tài khoản và phân quyền

| Mã | Quy tắc |
|---|---|
| R-22 | Email là duy nhất. Mật khẩu được mã hóa, không lưu dạng chữ thường. |
| R-23 | Tài khoản bị khóa không đăng nhập được. |
| R-24 | Chỉ tài khoản có quyền admin mới vào được `/admin`. |
| R-25 | Thành viên chỉ xem được đơn hàng và cuộc trò chuyện của chính mình. |

### 3.6 Chat

| Mã | Quy tắc |
|---|---|
| R-26 | Chỉ thành viên đã đăng nhập mới chat được. Khách vãng lai bấm nút chat sẽ được mời đăng nhập. |
| R-27 | Mỗi thành viên có đúng một cuộc trò chuyện với cửa hàng; lịch sử tin nhắn được giữ lại. |
| R-28 | Tin nhắn chỉ là chữ, tối đa 1.000 ký tự, không gửi tin rỗng. |
| R-29 | Trình duyệt hỏi máy chủ vài giây một lần để lấy tin mới (AJAX). |
| R-30 | Hộp thư admin đánh dấu cuộc trò chuyện có tin chưa đọc. |

## 4. Danh sách sơ đồ

| # | Sơ đồ | File | Trả lời câu hỏi |
|---|---|---|---|
| 1 | Use case tổng quát | [01-use-case.mmd](diagrams/01-use-case.mmd) | Ai làm được gì? |
| 2 | Tìm kiếm và lọc sản phẩm | [02-tim-kiem-loc.mmd](diagrams/02-tim-kiem-loc.mmd) | Khách tìm sản phẩm như thế nào? |
| 3 | Giỏ hàng và mã giảm giá | [03-gio-hang-ma-giam-gia.mmd](diagrams/03-gio-hang-ma-giam-gia.mmd) | Giỏ hàng và mã giảm giá được kiểm tra ra sao? |
| 4 | Đặt hàng | [04-dat-hang.mmd](diagrams/04-dat-hang.mmd) | Từ giỏ hàng đến đơn hàng gồm những bước nào? |
| 5 | Vòng đời đơn hàng | [05-trang-thai-don-hang.mmd](diagrams/05-trang-thai-don-hang.mmd) | Đơn đi qua những trạng thái nào, kho thay đổi lúc nào? |
| 6 | Admin xử lý đơn | [06-admin-xu-ly-don.mmd](diagrams/06-admin-xu-ly-don.mmd) | Admin làm gì với một đơn mới? |
| 7 | Quản lý sản phẩm | [07-quan-ly-san-pham.mmd](diagrams/07-quan-ly-san-pham.mmd) | Thêm, sửa, xóa sản phẩm được kiểm tra thế nào? |
| 8 | Chat | [08-chat.mmd](diagrams/08-chat.mmd) | Tin nhắn đi từ khách đến admin và ngược lại ra sao? |
| 9 | ERD | [09-erd.mmd](diagrams/09-erd.mmd) | Dữ liệu được lưu trong những bảng nào? |

## 5. Điểm còn chờ quyết định

- Giỏ hàng lưu trong session (đơn giản, ERD đang vẽ theo hướng này) hay trong bảng `carts` / `cart_items`.
- Định dạng mã đơn hàng, ví dụ `VC-YYYYMMDD-XXXX`.
