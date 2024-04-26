

<h1>Employee Management System</h1>

## Introduction

This Laravel project is an Employee Management System designed to streamline various HR-related tasks such as managing employees, departments, salaries, and leave applications. It utilizes Laravel 8 and follows the repository pattern for efficient data access. Token-based authentication using tymon/jwt-auth ensures secure access to the system, with separate tokens for employees and administrators. Role-based permissions are implemented using the Spatie package, with an 'admin' role having CRUD permissions for employees, limited permissions for departments and salaries, and the ability to approve leave applications.

## Feature

- <b>Token-Based Authentication:</b> Utilizes tymon/jwt-auth for secure token-based authentication.
- <b>Roles and Permissions:</b> Administrators have CRUD permissions for employees and limited permissions for departments and salaries. Employees have restricted access.
- <b>Repository Pattern:</b> Follows the repository pattern for efficient data access and separation of concerns.
- <b>Exception Handling:</b> Custom helper functions are implemented for exception handling to ensure graceful error handling.
- <b>Response Service Provider:</b> Implements ResponseService provider to log errors in the database for further analysis and debugging.
- <b>Task Scheduler:</b> Added a task scheduler to run every hour, fetching leave applications that are 24 hours old and unapproved, and automatically approving them.
- <b>Forget Password Feature:</b> Users can reset their passwords by providing their email address, receiving an OTP, and setting a new password.
- <b>Email Verification:</b> Implemented email verification for users upon registration, enhancing security and ensuring valid email addresses.
- <b>Queue Jobs:</b> All email-related tasks are executed using queue jobs to reduce response time and optimize performance.

## Installation

1. <b>Clone the repository</b>: `git clone https://github.com/your-repo.git`
2. <b>Install composer dependencies</b>: `composer install`
3. <b>Set up your environment variables</b>: Copy `.env.example` to `.env` and configure your database connection and other environment variables.
4. <b>Generate application key</b>: `php artisan key:generate`
5. <b>Migrate and seed the database</b>: `php artisan migrate --seed`
6. <b>Generate JWT secret</b>: `php artisan jwt:secret`
7. <b>Run scheduled tasks and queue workers</b>: 
   - To execute scheduled tasks, run: `php artisan schedule:work`
   - To process queued jobs, run: `php artisan queue:work`

### Scheduler Command

To approve unapproved leaves, a scheduler command is available. This command runs hourly to fetch and approve leave applications that are 24 hours old and still unapproved.
  - <b>php artisan approve:unapproved_leaves</b>

### Usage

1. Register as an administrator or employee:
   - Navigate to the registration page and provide the required information, including your role (admin or employee).
   - Upon successful registration, you will receive a confirmation email with instructions to verify your email address.

2. Log in with your credentials to access the system:
   - Enter your registered email address and password on the login page.
   - Once authenticated, you will be redirected to the dashboard based on your role.

3. For Administrators:
   - Access admin features by prefixing routes with `/admin`:
     - Example: `https://your-domain.com/admin/dashboard`
   - Create and manage employees:
     - Add new employees, update their information, or deactivate accounts as needed.
   - Approve leave applications:
     - Review leave applications submitted by employees and approve or reject them accordingly.
   - Distribute salaries to employees:
     - Manage salary payments, view payment history, and generate salary reports.
   - View department details:
     - Access information about different departments within the organization, including staff members and budgets.

4. For Employees:
   - Access employee features without any route prefix:
     - Example: `https://your-domain.com/dashboard`
   - Apply for leave:
     - Submit leave requests with details such as start date, end date, and reason for leave.
   - View salary details:
     - Check your salary history, deductions, and bonuses.
   - Update profile:
     - Edit personal information, contact details, and emergency contacts.

5. Forget Password Feature:
   - If you forget your password, click on the "Forgot Password" link on the login page.
   - Provide your registered email address to receive a password reset link and OTP.
   - Enter the OTP received via email and set a new password to regain access to your account.

6. Email Verification:
   - Upon registration, check your email inbox for a verification message.
   - Click on the verification link to confirm your email address and activate your account.
   - Once verified, you can proceed to log in to the system using your credentials.

7. Queue Jobs:
   - All email-related tasks, including forget password emails and email verification messages, are processed asynchronously using queue jobs.
   - This ensures efficient handling of email tasks without impacting the responsiveness of the application.

8. Token-Based Authentication:
   - Access to the system is secured using token-based authentication provided by tymon/jwt-auth.
   - Each user, whether an administrator or employee, receives a unique JWT token upon successful login, which is used to authenticate subsequent requests.

## Contributing

Contributions are welcome! Please follow the Contributing Guidelines for more details.

## License

This project is open-source software, but the license terms are not yet determined. See the [LICENSE.md](LICENSE.md) file for details once it's updated.

## Acknowledgments

- Laravel Framework
- tymon/jwt-auth
- Spatie Permissions
