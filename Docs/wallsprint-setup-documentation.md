# WallSprint WordPress Installation Documentation

## Overview

This document outlines the complete setup process for the WallSprint WordPress site, including WordPress installation, database configuration, and Nginx web server setup.

## Server Information

- **Server IP Address**: 192.168.0.124
- **Web Server**: Nginx
- **Port**: 8090
- **Domain/Hostname**: wallsprint.localhost

## WordPress Installation

### 1. Download and Extract WordPress

```bash
wget https://wordpress.org/latest.zip
unzip latest.zip
mv wordpress/* .
```

### 2. Database Configuration

```bash
# Create MySQL database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS wallsprint_wp;"

# Create database user
mysql -u root -e "CREATE USER IF NOT EXISTS 'wallsprint'@'localhost' IDENTIFIED BY 'wallsprint@212345';"

# Grant privileges
mysql -u root -e "GRANT ALL PRIVILEGES ON wallsprint_wp.* TO 'wallsprint'@'localhost'; FLUSH PRIVILEGES;"
```

### 3. WordPress Configuration File

```bash
# Copy sample config file
cp wp-config-sample.php wp-config.php

# Update database credentials
sed -i "s/database_name_here/wallsprint_wp/g" wp-config.php
sed -i "s/username_here/wallsprint/g" wp-config.php
sed -i "s/password_here/wallsprint@212345/g" wp-config.php

# Add security keys
curl -s https://api.wordpress.org/secret-key/1.1/salt/ >> wp-config.php
```

### 4. File Permissions

```bash
# Set proper permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chown -R nobody:nogroup .
```

## Nginx Server Configuration

### 1. Server Block Configuration

The Nginx configuration file is located at: `/etc/nginx/conf.d/wallsprint.conf`

Key configuration details:
```nginx
server {
    listen 8090;
    http2 on;
    server_name wallsprint.localhost 192.168.0.124;
    
    # SSL configuration
    ssl_certificate /etc/letsencrypt/live/biwillzcomputers.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/biwillzcomputers.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    
    # Main application root
    root /var/www/html/wallsprint;
    index index.php index.html;
    
    # Handle PHP files
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        # Additional PHP configurations...
    }
    
    # Security and other configurations...
}
```

### 2. Applied Configuration Changes

- Changed listening port from 8089 to 8090
- Added server IP address (192.168.0.124) to server_name directive

### 3. Reload Nginx

```bash
systemctl reload nginx
```

## Database Information

- **Database Name**: wallsprint_wp
- **Database User**: wallsprint
- **Database Password**: wallsprint@212345
- **Database Host**: localhost

## Accessing the Website

The WordPress site can be accessed using:

- **Local access**: http://wallsprint.localhost:8090
- **Remote access**: http://192.168.0.124:8090

## WordPress Admin

After completing the web-based setup, the WordPress admin dashboard will be available at:

- http://192.168.0.124:8090/wp-admin/

## Troubleshooting

### Common Issues

1. **Port Conflicts**: If another service is using port 8090, change to a different port in the Nginx configuration file.

2. **PHP Processing Issues**: Verify the PHP-FPM socket path in the Nginx configuration matches the actual path of the PHP-FPM socket.

3. **Permission Issues**: If WordPress cannot write to directories, check file permissions.

### Checking Server Status

```bash
# Check Nginx status
systemctl status nginx

# Check port listening
netstat -tulpn | grep 8090

# Check Nginx configuration
nginx -t
```

## Maintenance

### Updating WordPress

Updates can be performed through the WordPress admin dashboard or manually by downloading and replacing files.

### Backup Strategy

Regular backups should include:
- WordPress files in /var/www/html/wallsprint
- MySQL database (wallsprint_wp)

## Security Considerations

- Keep WordPress, themes, and plugins updated
- Use strong passwords
- Consider implementing a security plugin
- Regularly check for file integrity
- Consider setting up a Web Application Firewall (WAF)
