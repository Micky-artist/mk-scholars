# MK Scholars - Global Scholarship Platform

A comprehensive web application that connects students with global scholarship opportunities, provides educational courses, and offers real-time support through an integrated chat system.

## 🌟 Features

### 🎓 Scholarship Management
- **Global Scholarship Database**: Browse and apply for scholarships from around the world
- **Advanced Filtering**: Filter by country, tags, and categories
- **Application System**: Complete scholarship applications with document uploads
- **Status Tracking**: Monitor application progress and status

### 📚 Educational Courses
- **Multiple Course Types**: English courses, coding courses, UCAT preparation
- **Subscription-based Access**: Pay-per-course or subscription models
- **Interactive Learning**: Video content, practice questions, and assessments
- **Progress Tracking**: Monitor learning progress and completion

### 💬 Real-time Chat Support
- **Live Chat System**: Real-time communication between users and administrators
- **File Sharing**: Upload and share documents, images, and files
- **Typing Indicators**: See when someone is typing
- **Message History**: Persistent chat history with date separators
- **Mobile Responsive**: Works seamlessly on all devices

### 👤 User Management
- **User Registration & Authentication**: Secure login/signup system
- **Profile Management**: Update personal information and preferences
- **Subscription Tracking**: View active and expired subscriptions
- **Dashboard**: Personalized user dashboard with statistics

### 🔧 Admin Panel
- **Comprehensive Admin Interface**: Manage users, scholarships, and content
- **Chat Management**: Respond to user inquiries and manage conversations
- **Content Management**: Upload and edit scholarships, courses, and services
- **User Analytics**: Track user activity and subscription statistics
- **File Management**: Handle user uploads and shared documents

### 💳 Payment Integration
- **Secure Payment Processing**: Integrated payment gateway
- **Multiple Subscription Plans**: Various pricing tiers for different services
- **Transaction Tracking**: Monitor payment status and history
- **Automatic Subscription Management**: Handle subscription renewals and expirations

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+**: Server-side scripting and logic
- **MySQL**: Database management system
- **Apache(XAMPP)**: Web server
- **Session Management**: Secure user session handling

### Frontend
- **HTML5/CSS3**: Modern, responsive web design
- **JavaScript/jQuery**: Interactive user interface
- **Bootstrap 5**: Responsive CSS framework
- **Font Awesome**: Icon library
- **AJAX**: Asynchronous data loading

### Real-time Features
- **Server-Sent Events (SSE)**: Real-time chat updates
- **WebSocket Alternative**: Polling-based real-time communication
- **File Upload Handling**: Secure file management system

## 📋 Prerequisites

Before setting up MK Scholars, ensure you have:

- **Web Server**: Apache(XAMPP)
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Git**: Version control system

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/goldensash01/mkscholars.git
cd mkscholars
```

### 2. Database Setup
Create a MySQL database named `mkscholars` and import the database schema:


### 3. Configuration
Update the database connection settings in `dbconnection/connection.php`:

```php
$db_config = [
    'host' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'mkscholars'
];
```

### 4. File Permissions
Set proper permissions for upload directories:

```bash
chmod 755 uploads/
chmod 755 uploads/user_*/
chmod 755 uploads/chat/
chmod 755 uploads/documents/
chmod 755 uploads/posts/
chmod 755 uploads/services/
```

### 5. Web Server Configuration
Configure your web server to point to the project directory and ensure PHP is enabled.

### 6. Environment Setup
For production deployment, consider using environment variables:



## 📁 Project Structure

```
mkscholars/
├── admin/                    # Admin panel files
│   ├── php/                 # Admin backend scripts
│   ├── assets/              # Admin CSS, JS, and libraries
│   ├── uploads/             # Admin file uploads
│   └── *.php               # Admin interface pages
├── php/                     # User backend scripts
│   ├── login.php           # Authentication
│   ├── chat_stream.php     # Real-time chat
│   ├── upload_file.php     # File upload handling
│   └── *.php              # Other backend functions
├── partials/               # Reusable PHP components
├── uploads/                # User file uploads
├── images/                 # Static images and assets
├── css/                    # Stylesheets
├── js/                     # JavaScript files
├── payment/                # Payment processing
├── dbconnection/           # Database configuration
├── vendor/                 # Third-party libraries
├── tinymce/               # Rich text editor
└── *.php                  # Main application pages
```

## 🔧 Configuration

### Database Configuration
The application supports local development:

- **Local Development**: Uses default MySQL settings

### Timezone Configuration
The application is configured for Rwanda timezone (`Africa/Kigali`) by default.

### File Upload Configuration
- **Maximum File Size**: Configurable in PHP settings
- **Allowed File Types**: Images, documents, and other common formats
- **Storage Location**: Organized by user ID and type

## 🚀 Usage

### For Users
1. **Registration**: Create an account at `/sign-up`
2. **Browse Scholarships**: View available opportunities at `/applications`
3. **Apply for Scholarships**: Complete application forms with required documents
4. **Enroll in Courses**: Subscribe to educational courses
5. **Chat Support**: Get real-time help through the chat system
6. **Manage Profile**: Update personal information and view subscriptions

### For Administrators
1. **Admin Login**: Access admin panel at `/admin/authentication-login`
2. **Manage Content**: Upload and edit scholarships, courses, and services
3. **User Management**: Monitor user activity and manage accounts
4. **Chat Support**: Respond to user inquiries and manage conversations
5. **Analytics**: View system statistics and user engagement

## 🔒 Security Features

- **SQL Injection Prevention**: Prepared statements for all database queries
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Token-based form validation
- **Session Security**: Secure session management
- **File Upload Security**: Type validation and size restrictions
- **Password Hashing**: Secure password storage

## 📱 Mobile Responsiveness

The application is fully responsive and optimized for:
- Desktop computers
- Tablets
- Mobile phones
- Various screen sizes and orientations

## 🔄 Real-time Features

### Chat System
- **Real-time Messaging**: Instant message delivery
- **Typing Indicators**: Show when users are typing
- **File Sharing**: Upload and share documents
- **Message History**: Persistent chat logs
- **Status Indicators**: Online/offline status

### Performance Optimization
- **Efficient Polling**: Optimized for minimal server load
- **Message Deduplication**: Prevents duplicate messages
- **Caching**: Browser and server-side caching
- **Compression**: Gzip compression for faster loading

## 🛠️ Development

### Adding New Features
1. Create necessary database tables
2. Add backend PHP scripts in appropriate directories
3. Create frontend interfaces
4. Update navigation and routing
5. Test thoroughly across devices

### Customization
- **Themes**: Modify CSS variables for custom styling
- **Languages**: Add translation files for internationalization
- **Payment Gateways**: Integrate additional payment providers
- **Email Templates**: Customize notification emails

## 📊 Monitoring and Maintenance

### Regular Tasks
- **Database Backups**: Regular backup of user data
- **Log Monitoring**: Check error logs for issues
- **Performance Monitoring**: Monitor server response times
- **Security Updates**: Keep PHP and dependencies updated

### Troubleshooting
- **Chat Issues**: Check file permissions and database connections
- **Upload Problems**: Verify upload directory permissions
- **Payment Issues**: Check payment gateway configuration
- **Performance**: Monitor server resources and optimize queries

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request


## 🔄 Version History

- **v1.0.0**: Initial release with basic scholarship and course features
- **v1.1.0**: Added real-time chat system
- **v1.2.0**: Enhanced admin panel and user management
- **v1.3.0**: Improved mobile responsiveness and performance
- **v1.4.0**: Added payment integration and subscription management

---

**MK Scholars** - Connecting students with global opportunities through technology and education. 