
lấy file từ Google Drive hoặc các dịch vụ cloud khác sử dụng rclone:
nohup rclone copy gdrive:/backup/quanlotkhe.com/quanlotkhe-10-16-2024.tar.gz /home/ubuntu/restore & 

giải nén - giải nén ra folder "ols-docker-env" trong /home/ubuntu/restore
nohup tar -xzvf quanlotkhe-10-16-2024.tar.gz -C /home/ubuntu/restore &
