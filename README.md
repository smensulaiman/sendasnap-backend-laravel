# SendaSnap - Vehicle Management System

A comprehensive Laravel 12 backend API and dashboard panel for vehicle management with role-based access control, task management, and file upload capabilities.

## Features

### 🚗 Vehicle Management
- Complete vehicle information tracking (make, model, year, specifications, etc.)
- Vehicle photos and document management
- Consignee details management
- Vehicle status tracking (pending, in_yard, ready, sold)
- Advanced search and filtering
- Vehicle statistics and analytics

### 👥 User Management
- Role-based access control (Admin, Manager, Employee, Client)
- User authentication with Laravel Sanctum
- Profile management
- Password reset functionality

### 📋 Task Management
- Task creation and assignment
- Priority levels (low, medium, high, urgent)
- Status tracking (pending, running, completed, cancelled)
- Task attachments support
- Due date management
- Task filtering and search

### 📊 Dashboard
- Modern, responsive web interface
- Real-time statistics
- Recent activities overview
- Role-based navigation
- Beautiful UI with primary color #035294

### 🔧 API Features
- RESTful API with versioning (v1)
- JWT token authentication
- Comprehensive error handling
- Swagger/OpenAPI documentation
- File upload support
- Pagination and filtering

## Technology Stack

- **Backend**: Laravel 12
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **File Storage**: Laravel Storage
- **API Documentation**: Swagger/OpenAPI
- **Frontend**: Blade Templates with custom CSS
- **Testing**: PHPUnit with Pest

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd sendasnap-backend-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sendasnap
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seed data**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Login Credentials

The seeder creates several test users:

- **Admin**: admin@sendasnap.com / password
- **Manager**: manager@sendasnap.com / password
- **Employee**: john@sendasnap.com / password
- **Client**: client1@sendasnap.com / password

## API Documentation

Once the application is running, you can access the API documentation at:
- **Swagger UI**: `http://localhost:8000/api/documentation`

## API Endpoints

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout
- `GET /api/v1/auth/me` - Get current user
- `PUT /api/v1/auth/profile` - Update profile
- `POST /api/v1/auth/change-password` - Change password
- `POST /api/v1/auth/forgot-password` - Forgot password
- `POST /api/v1/auth/reset-password` - Reset password

### Vehicle Management
- `GET /api/v1/vehicles` - List vehicles
- `POST /api/v1/vehicles` - Create vehicle
- `GET /api/v1/vehicles/{id}` - Get vehicle details
- `PUT /api/v1/vehicles/{id}` - Update vehicle
- `DELETE /api/v1/vehicles/{id}` - Delete vehicle
- `POST /api/v1/vehicles/{id}/photos` - Upload vehicle photo
- `DELETE /api/v1/vehicles/{id}/photos/{photoId}` - Delete vehicle photo
- `GET /api/v1/vehicles/search` - Search vehicles
- `GET /api/v1/vehicles/stats` - Get vehicle statistics

### Task Management
- `GET /api/v1/tasks` - List tasks
- `POST /api/v1/tasks` - Create task
- `GET /api/v1/tasks/{id}` - Get task details
- `PUT /api/v1/tasks/{id}` - Update task
- `DELETE /api/v1/tasks/{id}` - Delete task
- `POST /api/v1/tasks/{id}/assign` - Assign task
- `PUT /api/v1/tasks/{id}/status` - Update task status
- `POST /api/v1/tasks/{id}/attachments` - Upload task attachment
- `DELETE /api/v1/tasks/{id}/attachments/{attachmentId}` - Delete task attachment
- `GET /api/v1/tasks/my-tasks` - Get my tasks
- `GET /api/v1/tasks/assigned-to-me` - Get assigned tasks

### User Management (Admin only)
- `GET /api/v1/users` - List users
- `POST /api/v1/users` - Create user
- `GET /api/v1/users/{id}` - Get user details
- `PUT /api/v1/users/{id}` - Update user
- `DELETE /api/v1/users/{id}` - Delete user
- `POST /api/v1/users/{id}/assign-role` - Assign role

## Database Schema

### Users Table
- id, name, email, password, role, phone, avatar, email_verified_at, created_at, updated_at

### Vehicles Table
- id, serial_number, make, model, chassis_model, cc, year, color
- vehicle_buy_date, auction_ship_number, net_weight, area
- length, width, height, plate_number, buying_price
- expected_yard_date, rikso_from, rikso_to, rikso_cost, rikso_company
- auction_sheet, tohon_copy, status, created_by, created_at, updated_at

### Tasks Table
- id, title, description, work_date, work_time, status, priority
- vehicle_id, assigned_to, created_by, due_date, completed_at
- created_at, updated_at

### Additional Tables
- vehicle_photos, consignee_details, task_attachments
- roles, permissions, role_permissions, user_roles (Spatie Permission)

## Testing

Run the test suite:
```bash
php artisan test
```

## File Storage

The application uses Laravel's storage system for file uploads:
- Vehicle photos: `storage/app/public/vehicle-photos/`
- Task attachments: `storage/app/public/task-attachments/`
- Documents: `storage/app/public/documents/`
- Avatars: `storage/app/public/avatars/`

Make sure to create a symbolic link:
```bash
php artisan storage:link
```

## Role Permissions

### Admin
- Full access to all features
- User management
- All CRUD operations

### Manager
- Vehicle management
- Task management
- User management (except admins)
- View all data

### Employee
- View vehicles
- Manage assigned tasks
- Upload files
- Limited data access

### Client
- View assigned vehicles
- Basic profile management
- Limited access

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, email admin@sendasnap.com or create an issue in the repository.