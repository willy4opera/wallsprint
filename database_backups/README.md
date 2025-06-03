# Database Backup Information

This directory contains automated database backups for the WordPress installation in both compressed and uncompressed formats.

## Backup Script Usage

Run the backup script:
```bash
./backup_database.sh
```

## Backup Details

- Two backup formats are created for each backup:
  * Uncompressed SQL (.sql) - approximately 21MB
  * Compressed GZ (.sql.gz) - approximately 1.9MB
- Backup naming format: wallsprint_wp_backup_YYYYMMDD_HHMMSS.[sql|sql.gz]
- Backups older than 30 days are automatically removed
- Each backup contains:
  * Full database structure
  * All table data
  * WordPress options and settings
  * Post and page content
  * User information
  * Plugin settings

## Restore Process

To restore from compressed backup:
1. Uncompress the backup file:
   ```bash
   gunzip backup_filename.sql.gz
   ```
2. Restore the database:
   ```bash
   mysql -u wallsprint -p wallsprint_wp < backup_filename.sql
   ```

To restore from uncompressed backup:
```bash
mysql -u wallsprint -p wallsprint_wp < backup_filename.sql
```

## Security Note

- Keep these backups secure
- Don't commit backups to version control
- Regularly verify backup integrity
- Store copies in a separate location
- Both formats are kept for redundancy and flexibility

## Space Considerations

- Uncompressed backups (.sql): ~21MB
- Compressed backups (.sql.gz): ~1.9MB
- Compression ratio: ~91% space saving
- Keep both formats for:
  * Quick restore (.sql)
  * Efficient storage (.sql.gz)
  * Maximum compatibility

## Automated Backup Schedule

Add to crontab for automated backups:
```bash
# Run backup daily at 2 AM
0 2 * * * /var/www/html/wallsprint/database_backups/backup_database.sh
```
