### 1. **Dừng các container hiện tại**

Dừng toàn bộ các container đang chạy.

```bash
cd /home/ubuntu/ols-docker-env
docker compose down
```

### 2. **Xóa toàn bộ thư mục hiện tại**

Xóa thư mục cũ để đảm bảo không còn dữ liệu hoặc file lỗi trước khi restore.

```bash
sudo rm -rf /home/ubuntu/ols-docker-env
```

**Chú ý**: Đảm bảo rằng thư mục chứa dữ liệu quan trọng đã được backup trước khi thực hiện bước này.

### 3. **Sử dụng rclone để lấy file backup từ Google Drive**

Bước này sử dụng rclone để sao chép file backup từ Google Drive về máy.

```bash
nohup rclone copy gdrive:/backup/quanlotkhe.com/quanlotkhe-10-21-2024.tar.gz /home/ubuntu/restore &
```

Lệnh trên sẽ sao chép file backup từ Google Drive về máy VPS vào thư mục `/home/ubuntu/restore`. **`nohup`** giúp giữ cho quá trình sao chép tiếp tục chạy ngay cả khi phiên SSH bị ngắt.

### 4. **Giải nén file backup**

Khi file đã được sao chép, bạn cần giải nén file backup vào thư mục `/home/ubuntu/ols-docker-env`.

```bash
nohup tar -xzvf /home/ubuntu/restore/quanlotkhe-10-21-2024.tar.gz -C /home/ubuntu/ &
```

Lệnh này sẽ giải nén file `.tar.gz` vào đúng thư mục bạn cần để khôi phục dữ liệu.

### 5. **Khôi phục file `.env`**

Khôi phục file .env trở lại thư mục đã giải nén.

```bash
cp /home/ubuntu/.env.bk /home/ubuntu/ols-docker-env/.env
```

### 6. **Khởi động lại các container**

Vào thư mục chứa Docker Compose và khởi động lại toàn bộ container:

```bash
cd /home/ubuntu/ols-docker-env
docker compose up -d
```

### 7. **cronjob**

```bash
# copy script vào container litespeed:
docker cp convert_to_webp.sh litespeed:/home/ubuntu

# truy cập vào litespeed container:
docker exec -it litespeed bash

# cài vim, setup cron job:
apt update
apt install vim -y
0 3 * * * /bin/bash /home/ubuntu/convert_to_webp.sh >> /home/ubuntu/convert_to_webp.log 2>&1

# test:
nohup /bin/bash /home/ubuntu/convert_to_webp.sh >> /home/ubuntu/convert_to_webp.log 2>&1 &

```
---

### Tổng hợp toàn bộ các lệnh:

```bash
# Dừng các container
cd /home/ubuntu/ols-docker-env
docker-compose down

# Xóa toàn bộ thư mục cũ
sudo rm -rf /home/ubuntu/ols-docker-env

# Sao chép file từ Google Drive
nohup rclone copy gdrive:/backup/quanlotkhe.com/quanlotkhe-10-21-2024.tar.gz /home/ubuntu/restore &

# Giải nén file
nohup tar -xzvf /home/ubuntu/restore/quanlotkhe-10-21-2024.tar.gz -C /home/ubuntu/ &

# Khôi phục file .env
cp /home/ubuntu/.env.bk /home/ubuntu/ols-docker-env/.env

# Khởi động lại container
cd /home/ubuntu/ols-docker-env
docker-compose up -d

# setup cron job convert image sang format webp 
0 3 * * * /bin/bash /home/ubuntu/convert_to_webp.sh >> /home/ubuntu/convert_to_webp.log 2>&1
```