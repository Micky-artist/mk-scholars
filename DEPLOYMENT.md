# MK Scholars Deployment Guide

## Environment Setup

### Local Development
1. Copy `.env.example` to `.env`
2. Configure your local database settings in `.env`
3. Ensure XAMPP MySQL is running

### Production Setup
1. **NEVER edit `.env` on production server directly**
2. Use GitHub Actions for deployment
3. Configure production settings via hosting panel environment variables
4. **Database updates**: SSH into Hostinger panel and update database directly (as per previous developer)

## Required Environment Variables

### Database Configuration
- `DB_HOST` - Database server hostname
- `DB_PORT` - Database server port (default: 3306)
- `DB_NAME` - Database name
- `DB_USER` - Database username  
- `DB_PASSWORD` - Database password
- `DB_SOCKET` - MySQL socket path (local XAMPP only)

### Application Configuration
- `APP_ENV` - Environment (local/production)
- `SITE_URL` - Full site URL
- `SITE_NAME` - Site name
- `ADMIN_EMAIL` - Admin contact email

### Payment Configuration
- `FLUTTERWAVE_SECRET_KEY` - Flutterwave secret key
- `FLUTTERWAVE_PUBLIC_KEY` - Flutterwave public key
- `DEFAULT_CURRENCY` - Currency code (RWF)

### Company Information
- `COMPANY_NAME` - Company name
- `COMPANY_LOGO` - Company logo URL
- `SUPPORT_PHONE` - Support contact number

## Deployment Process

### Automated Deployment (Recommended)
1. Push changes to `main` branch
2. GitHub Actions automatically deploys to production
3. `.env` file is preserved (never overwritten)
4. Database connection is automatically tested

### Manual Deployment (Emergency Only)
1. SSH into server
2. Backup current `.env` file
3. Deploy code manually
4. Restore `.env` file
5. Test database connection

## Database Schema Updates

### Current Process (Previous Developer Method)
1. SQL files are in `/sql/` directory
2. SSH into Hostinger hosting panel
3. Update database directly via phpMyAdmin or panel
4. Manual import required for schema changes
5. No automatic schema updates detected

### Alternative Methods
- Use `mysql` command line via SSH
- Import SQL files through hosting panel
- Use database management tools in Hostinger cPanel

### Future Improvements
- Consider migration system for schema updates
- Version control for database changes
- Automated rollback capabilities

## Security Notes

⚠️ **CRITICAL:**
- `.env` files are in `.gitignore` (NEVER committed)
- Production secrets stored in GitHub Secrets
- Database credentials never exposed in code

## Team Collaboration

### Adding New Environment Variables
1. Update `.env.example` with new variable
2. Update local `.env` with your value
3. Add production value via hosting panel
4. Document variable in this file
5. Update relevant code to use `getenv()`

### Code Standards
- Use `getenv('VAR_NAME')` for environment variables
- Provide fallback values: `getenv('VAR') ?: 'default'`
- Document all new environment variables
- Test both local and production environments

## Troubleshooting

### Database Connection Issues
1. Check `.env` file exists and is readable
2. Verify environment variables are set correctly
3. Test connection: `your-site.com/db-test.php`
4. Check error logs for detailed messages

### Deployment Issues
1. Verify GitHub Secrets are configured
2. Check deployment logs in Actions tab
3. Ensure server permissions are correct
4. Verify `.env` file exists on server

## Emergency Contacts

- Database: Check hosting panel
- Server: Hostinger support
- Code: Development team lead
