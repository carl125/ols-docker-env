Các script mà LiteSpeedTech cung cấp trong thư mục `bin` tương tác với các thành phần khác nhau của hệ thống như **LiteSpeed Web Server**, **MariaDB**, và **Redis**. Dưới đây là hướng dẫn để xác định **container** tương ứng mà mỗi script cần được chạy:

### 1. **`bash bin/webadmin.sh my_password`**
   - **Chức năng**: Đặt mật khẩu quản trị cho LiteSpeed WebAdmin.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/webadmin.sh my_password
     ```

### 2. **`bash bin/demosite.sh`**
   - **Chức năng**: Thiết lập một trang demo WordPress với domain mặc định.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/demosite.sh
     ```

### 3. **`bash bin/domain.sh [-A, --add] example.com`**
   - **Chức năng**: Tạo một domain và Virtual Host cho trang web.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/domain.sh -A example.com
     ```

### 4. **`bash bin/domain.sh [-D, --del] example.com`**
   - **Chức năng**: Xóa domain và Virtual Host.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/domain.sh -D example.com
     ```

### 5. **`bash bin/database.sh [-D, --domain] example.com`**
   - **Chức năng**: Tạo cơ sở dữ liệu và người dùng cho domain.
   - **Chạy trong container**: **MySQL (MariaDB)**.
   - **Cách chạy**:
     ```bash
     docker exec -it ols-docker-env-mysql-1 bash bin/database.sh -D example.com
     ```

### 6. **`bash bin/database.sh [-D, --domain] example.com [-U, --user] USER_NAME [-P, --password] MY_PASS [-DB, --database] DATABASE_NAME`**
   - **Chức năng**: Tạo cơ sở dữ liệu với thông tin tùy chỉnh.
   - **Chạy trong container**: **MySQL (MariaDB)**.
   - **Cách chạy**:
     ```bash
     docker exec -it ols-docker-env-mysql-1 bash bin/database.sh -D example.com -U USER_NAME -P MY_PASS -DB DATABASE_NAME
     ```

### 7. **`bash bin/appinstall.sh [-A, --app] wordpress [-D, --domain] example.com`**
   - **Chức năng**: Cài đặt ứng dụng (WordPress) cho một domain cụ thể.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/appinstall.sh -A wordpress -D example.com
     ```

### 8. **`bash bin/acme.sh [-I, --install] [-E, --email] EMAIL_ADDR`**
   - **Chức năng**: Cài đặt ACME (Let's Encrypt) để quản lý chứng chỉ SSL.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/acme.sh -I -E youremail@example.com
     ```

### 9. **`bash bin/acme.sh [-D, --domain] example.com`**
   - **Chức năng**: Áp dụng chứng chỉ SSL cho một domain.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/acme.sh -D example.com
     ```

### 10. **`bash bin/webadmin.sh [-U, --upgrade]`**
   - **Chức năng**: Nâng cấp phiên bản LiteSpeed Web Server.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/webadmin.sh -U
     ```

### 11. **`bash bin/webadmin.sh [-M, --mod-secure] enable`**
   - **Chức năng**: Bật **ModSecurity** trên LiteSpeed Web Server.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/webadmin.sh -M enable
     ```

### 12. **`bash bin/webadmin.sh [-M, --mod-secure] disable`**
   - **Chức năng**: Tắt **ModSecurity** trên LiteSpeed Web Server.
   - **Chạy trong container**: **LiteSpeed**.
   - **Cách chạy**:
     ```bash
     docker exec -it litespeed bash bin/webadmin.sh -M disable
     ```

### 13. **Truy cập Redis từ LiteSpeed Cache Plugin**
   - **Chức năng**: Kết nối Redis với LiteSpeed Cache.
   - **Thực hiện**: Bạn không cần chạy script nào cho Redis. Thay vào đó, bạn cấu hình từ **WordPress LiteSpeed Cache Plugin**:
     - Truy cập **WordPress Dashboard** > **LiteSpeed Cache** > **Cache** > **Object**.
     - Chọn phương thức **Redis** và nhập `redis` vào trường **Host**.

### Tóm tắt:

- Các script liên quan đến **LiteSpeed Web Server** (như `webadmin.sh`, `demosite.sh`, `domain.sh`, `appinstall.sh`) cần được chạy trong container **LiteSpeed**.
- Các script liên quan đến **cơ sở dữ liệu** (như `database.sh`) cần được chạy trong container **MySQL (MariaDB)**.
- Các script liên quan đến **ACME/SSL** cần được chạy trong container **LiteSpeed**.
- Redis được cấu hình thông qua **WordPress LSCache Plugin**, không cần script.

Nếu bạn gặp bất kỳ khó khăn nào khi chạy các script này, cứ cho mình biết nhé!