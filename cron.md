# Renew all ACME certificates every Sunday at midnight (00:00).
# The acme.sh script is executed from the directory /home/ubuntu/ols-docker-env.
# Logs are saved to /home/ubuntu/cron-log/acme_renew.log.
0 0 * * 7 cd /home/ubuntu/ols-docker-env && /usr/bin/bash bin/acme.sh --renew-all --force > /home/ubuntu/cron-log/acme_renew.log 2>&1

# Create a tar.gz backup of the ols-docker-env directory every day at 2 AM (02:00).
# The backup is stored in the /home/ubuntu/backup/quanlotkhe directory.
# Logs are saved to /home/ubuntu/cron-log/backup-quanlotkhe.log.
0 2 * * * sudo tar -czvf /home/ubuntu/backup/quanlotkhe/quanlotkhe-$(date +\%m-\%d-\%Y).tar.gz -C /home/ubuntu ols-docker-env > /home/ubuntu/cron-log/backup-quanlotkhe.log 2>&1

# Move the backup files from the VPS to Google Drive at 6 AM (06:00) every day.
# After uploading, the local backup files are deleted.
# Logs are saved to /home/ubuntu/cron-log/upload_backup_quanlotkhe.log.
0 6 * * * /usr/bin/rclone move /home/ubuntu/backup/quanlotkhe gdrive:/backup/quanlotkhe.com --log-file /home/ubuntu/cron-log/upload_backup_quanlotkhe.log --log-level INFO

# Delete backups on Google Drive older than 20 days at 7 AM (07:00) every day.
# Logs are saved to /home/ubuntu/cron-log/delete_old_backup_quanlotkhe.log.
0 7 * * * /usr/bin/rclone delete --min-age 20d gdrive:/backup/quanlotkhe.com > /home/ubuntu/cron-log/delete_old_backup_quanlotkhe.log 2>&1
