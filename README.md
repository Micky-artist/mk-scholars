# MK Scholars Platform

A comprehensive scholarship management platform built with PHP and MySQL, containerized with Docker for easy development and deployment.

## 🚀 Quick Start

### Prerequisites
- Docker (v20.10+)
- Docker Compose (v2.0+)

### Setup

1. **Clone the repository**
   ```bash
   git clone [your-repo-url]
   cd mkscholars
   ```

2. **Run the setup script**
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```

3. **Access the application**
   - Frontend: http://localhost:8000
   - Adminer (Database GUI): http://localhost:8080
     - System: MySQL
     - Server: db
     - Username: root
     - Password: [check .env file]
     - Database: mkscholars

## 🔧 Development

### Project Structure

```
├── docker/               # Docker configuration
│   ├── db/              # Database initialization scripts
│   └── php.ini          # PHP configuration
├── partials/            # Reusable PHP components
├── public/              # Web root
│   └── index.php        # Main entry point
├── .env.example         # Example environment variables
├── docker-compose.yml   # Docker services
├── Dockerfile           # PHP/Apache configuration
└── README.md            # This file
```

### Common Commands

| Command | Description |
|---------|-------------|
| `docker-compose up -d` | Start all services |
| `docker-compose down` | Stop all services |
| `docker-compose logs -f` | View service logs |
| `docker-compose exec app bash` | Access app container |
| `docker-compose exec db mysql -u root -p` | Access MySQL CLI |

## 🌟 Features

- User authentication and authorization
- Scholarship management
- Application processing
- Document uploads
- Messaging system
- Admin dashboard

## 🔒 Environment Variables

Copy `.env.example` to `.env` and update as needed:

```env
# Database
DB_HOST=db
DB_DATABASE=mkscholars
DB_USERNAME=root
DB_PASSWORD=your_secure_password

# App
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000
```

## 🛠 Troubleshooting

### Common Issues

1. **Port conflicts**
   - Check if ports 8000 (app) or 8080 (adminer) are in use
   - Update ports in `docker-compose.yml` if needed

2. **Database connection issues**
   - Verify database is running: `docker-compose ps`
   - Check logs: `docker-compose logs db`
   - Ensure `.env` has correct DB credentials

3. **File permissions**
   If you see permission errors:
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html
   ```

## 🤝 Contributing

1. Create a new branch: `git checkout -b feature/your-feature`
2. Make your changes
3. Test thoroughly
4. Submit a pull request

## 📄 License

This project is proprietary and confidential.

---

💡 **Tip**: Use `docker-compose down -v` to completely remove volumes (including database data) when needed.
