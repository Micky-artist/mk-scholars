# Writing Services Feature - MK Scholars

## Overview
The Writing Services feature has been added to the MK Scholars website, allowing users to request professional writing assistance for various academic and professional documents.

## Features

### User-Facing Features
1. **Writing Services Card on Home Page**
   - Located after the "STAY IN TOUCH" section
   - Features an attractive gradient design with service icons
   - Lists available services: Personal Statements, Essays, Resume & CV, Recommendation Letters, Motivation Letters, Cover Letters & Portfolios

2. **Dedicated Writing Services Page** (`/writing-services`)
   - Hero section with statistics and overview
   - Detailed service descriptions with features
   - "Why Choose Us" section highlighting benefits
   - Application form for service requests
   - Contact information sidebar

3. **Application Form**
   - Full name, email, phone number
   - Service type selection
   - Urgency level and deadline
   - Word count requirements
   - Detailed description
   - Additional information field
   - Form validation and error handling

### Admin Features
1. **Admin Dashboard Integration**
   - New "Writing Services" card in admin dashboard
   - Links to writing services management page

2. **Writing Services Management** (`/admin/writing-services`)
   - View all submitted requests
   - Update request status (pending, in progress, completed, cancelled)
   - Statistics dashboard showing request counts by status
   - Pagination for large numbers of requests
   - Contact information for each request

3. **Database Management**
   - Automatic table creation (`writing_services`)
   - Request tracking with timestamps
   - Status management system

## Technical Implementation

### Files Created/Modified

#### New Files
- `writing-services.php` - Main writing services page
- `process-writing-service.php` - Form processing and database insertion
- `admin/writing-services.php` - Admin management interface
- `WRITING_SERVICES_README.md` - This documentation

#### Modified Files
- `home.php` - Added Writing Services card
- `admin/home.php` - Added Writing Services admin card

### Database Schema
```sql
CREATE TABLE writing_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    urgency VARCHAR(50) NOT NULL,
    deadline DATE,
    word_count VARCHAR(50),
    description TEXT NOT NULL,
    additional_info TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Features
- **Responsive Design**: Mobile-first approach with Bootstrap
- **Form Validation**: Client-side and server-side validation
- **Email Notifications**: Automatic emails to admin and customer
- **Status Tracking**: Complete workflow management
- **Security**: Input sanitization and prepared statements
- **Error Handling**: Graceful error handling and user feedback

## Usage

### For Users
1. Visit the home page and click on the "Writing Services" card
2. Or navigate directly to `/writing-services`
3. Fill out the application form with your requirements
4. Submit the form to receive confirmation
5. Wait for contact from MK Scholars team

### For Administrators
1. Log into the admin panel
2. Click on the "Writing Services" card in the dashboard
3. View all submitted requests
4. Update request statuses as work progresses
5. Monitor statistics and manage workflow

## Service Types Available
- Personal Statements & Essays
- Resume & CV
- Recommendation Letters
- Motivation Letters
- Cover Letters & Portfolios

## Contact Information
- **Email**: mkscholars250@gmail.com
- **Phone**: +250798611161

## Technical Requirements
- PHP 7.4+
- MySQL 5.7+
- Modern web browser with JavaScript enabled
- SMTP server for email notifications (optional)

## Security Features
- Input sanitization and validation
- Prepared SQL statements
- CSRF protection through session management
- Admin authentication required for management
- Error logging for debugging

## Future Enhancements
- File upload capability for documents
- Payment integration
- Progress tracking for users
- Automated status updates
- Integration with external writing services
- Analytics and reporting dashboard

## Support
For technical support or questions about the Writing Services feature, contact the development team or refer to the main MK Scholars documentation.
