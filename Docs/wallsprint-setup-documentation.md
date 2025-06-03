# Wallsprint WordPress Setup Documentation

## Environment Configuration

The WordPress installation uses environment variables for configuration, making it portable and secure across different environments.

### Environment Files

- `.env`: Contains actual configuration values (not version controlled)
- `.env.example`: Template for required environment variables (version controlled)

### Required Environment Variables

```env
# Database Configuration
DB_NAME=database_name
DB_USER=database_user
DB_PASSWORD=database_password
DB_HOST=database_host
DB_CHARSET=utf8
DB_COLLATE=

# WordPress Debug
WP_DEBUG=true/false
WP_DEBUG_LOG=true/false
WP_DEBUG_DISPLAY=false
WP_MEMORY_LIMIT=256M

# Database Connection
WP_USE_EXT_MYSQL=false
```

## Dynamic URL Configuration

The installation uses dynamic URL detection, allowing the site to work across different environments without manual URL configuration.

### URL Handling

- URLs are dynamically detected based on the current server environment
- Supports both HTTP and HTTPS protocols
- Handles custom port numbers automatically
- Special handling for CLI operations

### Database URL Settings

The database maintains placeholder values for site URLs:
- home: {{site_url}}
- siteurl: {{site_url}}

These placeholders allow WordPress to use the dynamic URL detection in wp-config.php.

## Configuration Files

### wp-config.php

The main configuration file includes:
- Environment variable loading from .env
- Dynamic URL detection
- Database configuration
- Debug settings
- Memory limits
- Security keys and salts

### Features

1. Environment-based Configuration:
   - Secure credential management
   - Easy environment switching
   - No hardcoded values

2. Dynamic URL Detection:
   - Automatic protocol detection (http/https)
   - Server hostname detection
   - Port number handling
   - CLI mode support

3. Database Configuration:
   - Environment-based credentials
   - UTF-8 character set
   - Direct MySQL connection

## Migration Steps

When migrating to a new environment:

1. Copy `.env.example` to `.env`
2. Update `.env` with new environment values
3. No need to update URLs in the database
4. Ensure proper file permissions:
   - wp-config.php: 644
   - .env: 600

## Development Guidelines

1. Never commit `.env` file
2. Always update `.env.example` when adding new environment variables
3. Maintain the {{site_url}} placeholders in the database
4. Use environment variables for all configuration values

## Troubleshooting

1. Database Connection Issues:
   - Verify .env database credentials
   - Check database host accessibility
   - Ensure proper user permissions

2. URL Issues:
   - Verify dynamic URL detection in wp-config.php
   - Check for hardcoded URLs in database
   - Verify server environment variables

3. Permission Issues:
   - Check file permissions
   - Verify web server user access
   - Ensure .env file is readable

## Security Considerations

1. Environment Variables:
   - Protect .env file
   - Use strong passwords
   - Limit database user permissions

2. File Permissions:
   - Restrict .env access
   - Maintain secure wp-config.php permissions
   - Regular security audits

## Maintenance

1. Regular Tasks:
   - Update environment variables as needed
   - Review and update debug settings
   - Monitor log files
   - Backup database and files

2. Updates:
   - Document any configuration changes
   - Update .env.example as needed
   - Maintain version control
   - Test in staging environment first
