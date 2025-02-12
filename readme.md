Cấu trúc Plugin

Thư mục Plugin nằm trong Plugin Wordpress là protect-password-content

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
- Sử dụng đếm ngược để lấy mật khẩu ở trang B nhập mật khẩu vào link download ở trang A mở khóa link download và nội dung