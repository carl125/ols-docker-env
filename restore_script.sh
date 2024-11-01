#!/bin/bash

# Kiểm tra đầu vào
if [ -z "$1" ]; then
    echo "Vui lòng cung cấp tên file backup (ví dụ: ./restore_script.sh quanlotkhe-10-21-2024.tar.gz)"
    exit 1
fi

BACKUP_FILE=$1
RESTORE_DIR="/home/ubuntu/restore"
DOCKER_ENV_DIR="/home/ubuntu/ols-docker-env"
ENV_BK="/home/ubuntu/.env.bk"

echo "Bắt đầu quá trình restore với file backup: $BACKUP_FILE"

# Bước 1: Dừng các container
echo "Dừng các container hiện tại..."
cd $DOCKER_ENV_DIR
docker compose down

# Bước 2: Xóa toàn bộ thư mục cũ
echo "Xóa thư mục cũ..."
sudo rm -rf $DOCKER_ENV_DIR

# Bước 3: Sao chép file backup từ Google Drive
echo "Sao chép file từ Google Drive..."
mkdir -p $RESTORE_DIR
nohup rclone copy "gdrive:/backup/quanlotkhe.com/$BACKUP_FILE" $RESTORE_DIR &

# Đợi quá trình sao chép hoàn tất
wait
echo "Sao chép hoàn tất."

# Bước 4: Giải nén file backup
echo "Giải nén file backup..."
nohup tar -xzvf "$RESTORE_DIR/$BACKUP_FILE" -C /home/ubuntu/ &

# Đợi giải nén hoàn tất
wait
echo "Giải nén hoàn tất."

# Bước 5: Khôi phục file .env
echo "Khôi phục file .env..."
cp $ENV_BK "$DOCKER_ENV_DIR/.env"

# Bước 6: Khởi động lại container
echo "Khởi động các container..."
cd $DOCKER_ENV_DIR
docker compose up -d

# Bước 7: Copy script vào container LiteSpeed và thiết lập cron job
echo "Copy script vào container LiteSpeed và thiết lập cron job..."

chmod +x "$DOCKER_ENV_DIR/convert_to_webp.sh"

# Sao chép file script vào trong container
docker cp "$DOCKER_ENV_DIR/convert_to_webp.sh" litespeed:/home/ubuntu

# Thiết lập cron job bên trong container
docker exec litespeed bash -c "apt update && apt install webp -y && (crontab -l 2>/dev/null; echo '0 3 * * * /bin/bash /home/ubuntu/convert_to_webp.sh > /home/ubuntu/convert_to_webp.log 2>&1') | crontab -"

# Kiểm tra nếu cron job được tạo thành công
if docker exec litespeed crontab -l | grep -q "convert_to_webp.sh"; then
    echo "Cron job đã được thiết lập thành công trong container LiteSpeed."
else
    echo "Lỗi khi thiết lập cron job."
fi

# Xóa tất cả nội dung đã tải về
rm -rf "$RESTORE_DIR"/*

echo "Quá trình restore hoàn tất."