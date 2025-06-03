#!/bin/bash

# Configuration
DB_USER="wallsprint"
DB_PASS="wallsprint@212345"
DB_NAME="wallsprint_wp"
BACKUP_DIR="."
DAYS_TO_KEEP=30
LOG_FILE="/var/log/wallsprint-backup.log"

# Log function
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Create backup
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${DB_NAME}_backup_${TIMESTAMP}"

# Perform backup
log_message "Starting backup of $DB_NAME database..."

# Create uncompressed SQL backup
log_message "Creating .sql backup..."
if mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > "${BACKUP_FILE}.sql"; then
    chown nobody:nogroup "${BACKUP_FILE}.sql"
    chmod 640 "${BACKUP_FILE}.sql"
    log_message "SQL backup created successfully"
else
    log_message "Error creating SQL backup"
    exit 1
fi

# Create compressed backup
log_message "Creating compressed backup..."
if mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > "${BACKUP_FILE}.sql.gz"; then
    chown nobody:nogroup "${BACKUP_FILE}.sql.gz"
    chmod 640 "${BACKUP_FILE}.sql.gz"
    log_message "Compressed backup created successfully"
else
    log_message "Error creating compressed backup"
    exit 1
fi

# Remove old backups
log_message "Removing backups older than $DAYS_TO_KEEP days..."
find $BACKUP_DIR -name "*.sql" -type f -mtime +$DAYS_TO_KEEP -delete
find $BACKUP_DIR -name "*.sql.gz" -type f -mtime +$DAYS_TO_KEEP -delete

# List current backups
log_message "Current backups:"
log_message "Uncompressed backups (.sql):"
ls -lh *.sql 2>/dev/null || log_message "No .sql backups found"
log_message "Compressed backups (.sql.gz):"
ls -lh *.sql.gz 2>/dev/null || log_message "No .sql.gz backups found"

# Calculate and display sizes
if [ -f "${BACKUP_FILE}.sql" ]; then
    SQL_SIZE=$(du -h "${BACKUP_FILE}.sql" | cut -f1)
    log_message "Uncompressed backup size: $SQL_SIZE"
fi

if [ -f "${BACKUP_FILE}.sql.gz" ]; then
    GZ_SIZE=$(du -h "${BACKUP_FILE}.sql.gz" | cut -f1)
    log_message "Compressed backup size: $GZ_SIZE"
fi

log_message "Backup completed successfully!"
