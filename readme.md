Cấu trúc Plugin

Thư mục Plugin nằm trong Plugin Wordpress là 
- protect-password-content (Folder gốc dự án)
	- protect-password-content.php (File Plugin chính)
	- includes (Folder chức năng)
		- acp-api.php (File tạo và kết nối api)
		- acp-database.php (Tạo và kết nối database cơ sở dữ liệu)
		- acp-functions.php (File hàm xử lý các trang chức năng)
		- acp-settings.php (Cài đặt chức năng của plugin)

Chức năng chính của Plugin:

- Nhập mật khẩu để download tài liệu
- Nhập mật khẩu để xem nội dung đầy đủ
- Nút đếm ngược để lấy mật khẩu cho chức năng download và xem nội dung
- Nút đếm ngược lấy mật khẩu có 2 lựa chọn:
	1. Đếm ngược vượt mã 1 lần (Có thể cài đặt thời gian nút đếm ngược để hiện mật khẩu)
 	2. Nếu tích vào lựa chọn vượt mã 2 lần  (Lần đầu tiên kết thúc sẽ có thông báo nhấp vào liên kết bất kỳ trên trang web để chuyển trang và đếm ngược lần 2 kết thúc mới hiển thị mật khẩu)
  	3. Kiểm tra người dùng có đến từ google không? nếu đúng hiển thị nút đếm ngược. Đang kiểm tra nguồn truy cập bằng Analytics và GA4 của google chạy rất chuẩn 
- Sử dụng đếm ngược để lấy mật khẩu ở trang B (Nút lấy mật khẩu ở trang B) sử dụng api để kết nối nhập mật khẩu vào link download ở trang A mở khóa link download và nội dung
