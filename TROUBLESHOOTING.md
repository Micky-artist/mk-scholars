# 🛠 Troubleshooting Guide

This guide covers common issues and solutions when working with the MK Scholars platform.

## Table of Contents
- [Docker Issues](#docker-issues)
- [Database Issues](#database-issues)
- [Application Issues](#application-issues)
- [Performance Issues](#performance-issues)
- [Common Errors](#common-errors)

## Docker Issues

### Containers won't start
**Symptoms**: `docker-compose up` fails or containers exit immediately

**Solutions**:
1. Check logs: `docker-compose logs`
2. Ensure ports are available:
   ```bash
   # Check for processes using port 8000
   lsof -i :8000
   ```
3. Try rebuilding containers:
   ```bash
   docker-compose down
   docker-compose build --no-cache
   docker-compose up -d
   ```

### Volume permission issues
**Symptoms**: PHP can't write to directories or permission denied errors

**Solutions**:
```bash
# Fix permissions inside container
docker-compose exec app chown -R www-data:www-data /var/www/html

# Or fix locally (on host machine)
sudo chown -R $USER:$USER .
```

## Database Issues

### Can't connect to MySQL
**Symptoms**: Connection refused or access denied errors

**Solutions**:
1. Check if database is running:
   ```bash
   docker-compose ps
   ```
2. Check database logs:
   ```bash
   docker-compose logs db
   ```
3. Verify credentials in `.env` match `docker-compose.yml`
4. Try connecting manually:
   ```bash
   docker-compose exec db mysql -u root -p
   ```

### Database schema not loading
**Symptoms**: Tables missing or schema not applied

**Solutions**:
1. Manually import schema:
   ```bash
   docker-compose exec -T db mysql -u root -p"$MYSQL_ROOT_PASSWORD" < docker/db/init.sql
   docker-compose exec -T db mysql -u root -p"$MYSQL_ROOT_PASSWORD" < docker/db/seed.sql
   ```
2. Reset the database:
   ```bash
   docker-compose down -v
   docker-compose up -d
   ```

## Application Issues

### Session errors
**Symptoms**: `session_start()` warnings or session not persisting

**Solutions**:
1. Ensure session directory is writable:
   ```bash
   docker-compose exec app chmod -R 777 /var/lib/php/sessions
   ```
2. Check PHP logs:
   ```bash
   docker-compose exec app tail -f /var/log/apache2/error.log
   ```

### File upload issues
**Symptoms**: Uploads fail or files not saving

**Solutions**:
1. Check upload directory permissions:
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html/uploads
   ```
2. Verify PHP upload limits in `docker/php.ini`

## Performance Issues

### Slow page loads
**Solutions**:
1. Enable OPcache in `docker/php.ini`
2. Check database queries with slow query log:
   ```bash
   docker-compose exec db mysql -e "SET GLOBAL slow_query_log = 'ON';"
   docker-compose exec db mysql -e "SET GLOBAL long_query_time = 1;"
   ```

## Common Errors

### "Headers already sent"
**Solution**:
1. Check for whitespace before `<?php` or after `?>`
2. Ensure no output before `session_start()`

### "MySQL server has gone away"
**Solution**:
1. Increase MySQL timeouts in `docker-compose.yml`:
   ```yaml
   db:
     command: [
       '--wait_timeout=28800',
       '--interactive_timeout=28800'
     ]
   ```

### "Too many connections"
**Solution**:
1. Increase max connections in `docker-compose.yml`:
   ```yaml
   db:
     command: ['--max_connections=200']
   ```

## Getting Help

If you encounter an issue not covered here:
1. Check the logs: `docker-compose logs -f`
2. Search the codebase for error messages
3. Check for open/closed issues
4. Ask in the team chat with:
   - Exact error message
   - Steps to reproduce
   - Environment details

## Debugging Tips

1. Access the container shell:
   ```bash
   docker-compose exec app bash
   ```

2. View PHP errors:
   ```bash
   docker-compose exec app tail -f /var/log/apache2/error.log
   ```

3. Check database:
   ```bash
   docker-compose exec db mysql -u root -p"$MYSQL_ROOT_PASSWORD" mkscholars -e "SHOW TABLES;"
   ```
