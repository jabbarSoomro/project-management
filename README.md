# Project Management System

A robust and scalable **Full-Stack Project Management Application** built with **Laravel 10** (Backend API) and **Vue.js 3** (Frontend SPA), following **SOLID principles** and implementing a clean, maintainable architecture with **Repository** and **Service** layer patterns.

---

## 📋 Table of Contents

- [Features](#-features)
- [Architecture Overview](#-architecture-overview)
- [SOLID Principles Implementation](#-solid-principles-implementation)
- [Project Structure](#-project-structure)
- [Prerequisites](#-prerequisites)
- [Installation & Setup](#-installation--setup)
- [Frontend Setup (Vue.js SPA)](#-frontend-setup-vuejs-spa)
- [API Documentation](#-api-documentation)
- [Code Implementation Guide](#-code-implementation-guide)
- [Testing](#-testing)
- [Contributing](#-contributing)

---

## ✨ Features

### Core Functionality
- **User Authentication** - Secure login/logout using Laravel Sanctum
- **Project Management** - Create, view, and manage projects with pagination
- **Task Management** - Create and assign tasks within projects
- **Authorization** - Policy-based access control for projects and tasks
- **Email Notifications** - Automated email notifications for task assignments
- **Queue System** - Background job processing for email notifications
- **Soft Deletes** - Safe data deletion with recovery options
- **API Resources** - Consistent and structured JSON responses

### Frontend Features (Vue.js SPA)
- **Modern UI** - Responsive design with Tailwind CSS
- **Single Page Application** - Fast, seamless navigation with Vue Router
- **State Management** - Centralized auth state with Pinia
- **Protected Routes** - Route guards for authenticated pages
- **Real-time Updates** - Hot Module Replacement (HMR) during development
- **Token Management** - Automatic token injection in API requests

### Technical Features
- **Repository Pattern** - Abstraction layer for data access
- **Service Layer** - Business logic separation from controllers
- **Dependency Injection** - Loose coupling and testability
- **Form Request Validation** - Centralized validation logic
- **Policy Authorization** - Fine-grained access control
- **Database Indexing** - Optimized query performance
- **RESTful API** - Standard HTTP methods and status codes
- **Modern Build Tools** - Vite for fast development and optimized builds

---

## 🏗️ Architecture Overview

This application follows a **layered architecture** pattern:

```
┌─────────────────────────────────────────┐
│         Controller Layer                │  ← HTTP Request Handling
│  (ProjectController, TaskController)    │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│          Service Layer                  │  ← Business Logic
│  (ProjectService, TaskService)          │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│        Repository Layer                 │  ← Data Access
│  (ProjectRepository, TaskRepository)    │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│          Model Layer                    │  ← Database Interaction
│      (Project, Task, User)              │
└─────────────────────────────────────────┘
```

### Layer Responsibilities

| Layer | Responsibility | Example |
|-------|---------------|---------|
| **Controller** | Handle HTTP requests, validate input, return responses | `ProjectController::store()` |
| **Service** | Business logic, orchestration, job dispatching | `ProjectService::createProject()` |
| **Repository** | Database queries, data persistence | `ProjectRepository::create()` |
| **Model** | Database structure, relationships, casting | `Project`, `Task` |

---

## 🎯 SOLID Principles Implementation

This project strictly adheres to SOLID principles for maintainable and scalable code:

### 1️⃣ **Single Responsibility Principle (SRP)**

Each class has one, and only one, reason to change.

**Example:**
```php
// ✅ ProjectRepository - Only responsible for data access
class ProjectRepository implements ProjectRepositoryInterface
{
    public function create(array $data): Project
    {
        return Project::create($data);
    }
}

// ✅ ProjectService - Only responsible for business logic
class ProjectService
{
    public function createProject(array $data): Project
    {
        return $this->projectRepository->create($data);
    }
}

// ✅ ProjectController - Only responsible for HTTP handling
class ProjectController extends Controller
{
    public function store(CreateProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($data);
        return response()->json(['data' => new ProjectResource($project)]);
    }
}
```

### 2️⃣ **Open/Closed Principle (OCP)**

Classes are open for extension but closed for modification.

**Example:**
```php
// ✅ Interface allows extension without modifying existing code
interface ProjectRepositoryInterface
{
    public function create(array $data): Project;
    public function findById(int $id): ?Project;
}

// Can create new implementations without changing existing code
class CachedProjectRepository implements ProjectRepositoryInterface
{
    // New implementation with caching
}
```

### 3️⃣ **Liskov Substitution Principle (LSP)**

Objects should be replaceable with instances of their subtypes without altering correctness.

**Example:**
```php
// ✅ Any implementation of ProjectRepositoryInterface can be substituted
class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}
}

// Both work seamlessly
$service = new ProjectService(new ProjectRepository());
$service = new ProjectService(new CachedProjectRepository());
```

### 4️⃣ **Interface Segregation Principle (ISP)**

Clients should not be forced to depend on interfaces they don't use.

**Example:**
```php
// ✅ Specific interfaces for specific needs
interface ProjectRepositoryInterface
{
    public function create(array $data): Project;
    public function findById(int $id): ?Project;
    public function getPaginatedByUserId(int $userId, int $perPage = 10);
}

interface TaskRepositoryInterface
{
    public function create(array $data): Task;
    public function getByProjectId(int $projectId): Collection;
}
```

### 5️⃣ **Dependency Inversion Principle (DIP)**

Depend on abstractions, not concretions.

**Example:**
```php
// ✅ Service depends on interface (abstraction), not concrete class
class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository  // ← Interface
    ) {}
}

// ✅ Binding in RepositoryServiceProvider
public function register(): void
{
    $this->app->bind(
        ProjectRepositoryInterface::class,
        ProjectRepository::class
    );
}
```

---

## 📁 Project Structure

```
project-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php      # Authentication endpoints
│   │   │       ├── ProjectController.php   # Project CRUD operations
│   │   │       └── TaskController.php      # Task management
│   │   ├── Requests/
│   │   │   ├── CreateProjectRequest.php    # Project validation
│   │   │   └── CreateTaskRequest.php       # Task validation
│   │   └── Resources/
│   │       ├── ProjectResource.php         # Project JSON transformation
│   │       └── TaskResource.php            # Task JSON transformation
│   ├── Models/
│   │   ├── Project.php                     # Project model with relationships
│   │   ├── Task.php                        # Task model with relationships
│   │   └── User.php                        # User model
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── ProjectRepositoryInterface.php  # Project repository contract
│   │   │   └── TaskRepositoryInterface.php     # Task repository contract
│   │   ├── ProjectRepository.php           # Project data access implementation
│   │   └── TaskRepository.php              # Task data access implementation
│   ├── Services/
│   │   ├── ProjectService.php              # Project business logic
│   │   └── TaskService.php                 # Task business logic
│   ├── Jobs/
│   │   └── SendTaskNotificationJob.php     # Background email job
│   ├── Mail/
│   │   └── TaskAssignedNotification.php    # Email notification template
│   ├── Policies/
│   │   ├── ProjectPolicy.php               # Project authorization rules
│   │   └── TaskPolicy.php                  # Task authorization rules
│   └── Providers/
│       ├── RepositoryServiceProvider.php   # Repository bindings
│       └── AuthServiceProvider.php         # Policy registrations
├── database/
│   ├── migrations/                         # Database schema
│   ├── factories/                          # Model factories for testing
│   └── seeders/                            # Database seeders
├── resources/
│   ├── css/
│   │   └── app.css                         # Tailwind CSS imports
│   ├── js/
│   │   ├── App.vue                         # Root Vue component
│   │   ├── app.js                          # Vue application entry point
│   │   ├── bootstrap.js                    # Axios configuration
│   │   ├── components/                     # Reusable Vue components
│   │   ├── views/
│   │   │   ├── Login.vue                   # Login page
│   │   │   ├── Dashboard.vue               # Dashboard with project list
│   │   │   ├── ProjectCreate.vue           # Create project form
│   │   │   └── ProjectDetails.vue          # Project details with tasks
│   │   ├── router/
│   │   │   └── index.js                    # Vue Router configuration
│   │   └── stores/
│   │       └── auth.js                     # Pinia auth store
│   └── views/
│       └── welcome.blade.php               # SPA mount point
├── routes/
│   └── api.php                             # API route definitions
├── tests/                                  # Unit and feature tests
├── .env.example                            # Environment configuration template
├── composer.json                           # PHP dependencies
├── package.json                            # Node.js dependencies
├── vite.config.js                          # Vite configuration
├── tailwind.config.js                      # Tailwind CSS configuration
└── postman_collection.json                 # API testing collection
```

---

## 🔧 Prerequisites

Before you begin, ensure you have the following installed:

### Backend Requirements
- **PHP** >= 8.1
- **Composer** >= 2.0
- **MySQL** >= 5.7 or **MariaDB** >= 10.3

### Frontend Requirements
- **Node.js** >= 16.x (Required for Vue.js frontend)
- **npm** >= 8.x (Comes with Node.js)

### Other Tools
- **Git** - Version control

---

## 🚀 Installation & Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/jabbarSoomro/project-management.git
cd project-management
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies (Backend)
composer install

# Install Node.js dependencies (Frontend - Required)
npm install
```

### Step 3: Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 5: Run Migrations

```bash
# Create database tables
php artisan migrate

# (Optional) Seed the database with sample data
php artisan db:seed
```

### Step 6: Configure Queue System

Update `.env` for queue configuration:

```env
QUEUE_CONNECTION=database
```

Run migrations for queue tables (if not already done):

```bash
php artisan queue:table
php artisan migrate
```

### Step 7: Configure Mail Settings

Update `.env` with your mail configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@projectmanagement.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 8: Start the Application

```bash
# Start the development server
php artisan serve

# In a separate terminal, start the queue worker
php artisan queue:work
```

The API will be available at: `http://localhost:8000/api`

---

## 🎨 Frontend Setup (Vue.js SPA)

This project includes a **Vue.js 3** Single Page Application (SPA) frontend with modern tooling.

### Frontend Technology Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **Vue.js** | 3.5+ | Progressive JavaScript framework |
| **Vue Router** | 4.6+ | Client-side routing |
| **Pinia** | 2.3+ | State management |
| **Vite** | 5.0+ | Build tool and dev server |
| **Tailwind CSS** | 4.1+ | Utility-first CSS framework |
| **Axios** | 1.6+ | HTTP client for API calls |

### Frontend Features

- ✅ **Authentication** - Login/logout with token management
- ✅ **Dashboard** - Project overview and management
- ✅ **Project Management** - Create and view projects
- ✅ **Project Details** - View project with tasks
- ✅ **Route Guards** - Protected routes with authentication
- ✅ **State Management** - Centralized auth state with Pinia
- ✅ **Responsive Design** - Mobile-friendly UI with Tailwind CSS

### Frontend Project Structure

```
resources/
├── css/
│   └── app.css                    # Tailwind CSS imports
├── js/
│   ├── App.vue                    # Root Vue component
│   ├── app.js                     # Application entry point
│   ├── bootstrap.js               # Axios and Laravel Echo setup
│   ├── components/                # Reusable Vue components
│   ├── views/
│   │   ├── Login.vue              # Login page
│   │   ├── Dashboard.vue          # Dashboard with project list
│   │   ├── ProjectCreate.vue      # Create new project form
│   │   └── ProjectDetails.vue     # Project details with tasks
│   ├── router/
│   │   └── index.js               # Vue Router configuration
│   └── stores/
│       └── auth.js                # Pinia auth store
└── views/
    └── welcome.blade.php          # Laravel blade template (SPA mount point)
```

### Step 1: Install Frontend Dependencies

If you haven't already installed Node dependencies:

```bash
npm install
```

### Step 2: Start the Frontend Development Server

```bash
# Start Vite development server with hot module replacement (HMR)
npm run dev
```

The Vite dev server will start at `http://localhost:5173` (or another port if 5173 is busy).

### Step 3: Access the Application

Open your browser and navigate to:

```
http://localhost:8000
```

> **Note:** The Laravel backend serves the Vue.js SPA. Vite runs in the background for hot module replacement during development.

### Step 4: Build for Production

When ready to deploy, build the frontend assets:

```bash
# Build optimized production assets
npm run build
```

This will compile and minify your Vue.js application into the `public/build` directory.

### Frontend Configuration

#### API Base URL

The frontend is configured to communicate with the Laravel API. The base URL is automatically set based on your environment:

**Development:** `http://localhost:8000/api`

To change the API URL, update `resources/js/bootstrap.js`:

```javascript
axios.defaults.baseURL = 'http://localhost:8000/api';
```

#### Authentication Flow

1. **Login** - User submits credentials to `/api/login`
2. **Token Storage** - JWT token is stored in `localStorage`
3. **Axios Interceptor** - Token is automatically added to all API requests
4. **Route Guards** - Protected routes check for token before allowing access
5. **Logout** - Token is removed from `localStorage` and API

#### State Management with Pinia

The auth store (`resources/js/stores/auth.js`) manages:
- User authentication state
- Token persistence
- User profile data
- Login/logout actions

Example usage:

```javascript
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

// Login
await authStore.login({ email, password });

// Check if authenticated
if (authStore.isAuthenticated) {
  // User is logged in
}

// Logout
authStore.logout();
```

#### Available Routes

| Route | Component | Auth Required | Description |
|-------|-----------|---------------|-------------|
| `/login` | Login.vue | No | User login page |
| `/` | Dashboard.vue | Yes | Project dashboard |
| `/projects/create` | ProjectCreate.vue | Yes | Create new project |
| `/projects/:id` | ProjectDetails.vue | Yes | View project details |

### Development Workflow

1. **Backend:** Run `php artisan serve` (port 8000)
2. **Queue Worker:** Run `php artisan queue:work` (separate terminal)
3. **Frontend:** Run `npm run dev` (Vite dev server)
4. **Access:** Open `http://localhost:8000` in your browser

### Troubleshooting

#### Issue: Vite manifest not found

**Solution:** Run `npm run build` to generate the production assets, or ensure `npm run dev` is running for development.

#### Issue: API requests failing with CORS errors

**Solution:** Ensure your Laravel backend is running and the API base URL in `bootstrap.js` is correct.

#### Issue: Authentication not persisting

**Solution:** Check browser console for localStorage errors. Ensure cookies are enabled.

#### Issue: Hot Module Replacement (HMR) not working

**Solution:** 
1. Ensure `npm run dev` is running
2. Check that Vite is accessible at `http://localhost:5173`
3. Clear browser cache and restart Vite

---

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication

All endpoints except `/login` require authentication using **Bearer Token**.

#### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {your_token}
```

---

### 🔐 Authentication Endpoints

#### **POST** `/login`
Authenticate a user and receive an API token.

**Request Body:**
```json
{
    "email": "user1@example.com",
    "password": "password"
}
```

**Response (200 OK):**
```json
{
    "message": "Login successful",
    "token": "1|abcdef123456...",
    "user": {
        "id": 1,
        "name": "User One",
        "email": "user1@example.com"
    }
}
```

---

#### **POST** `/logout`
Logout the authenticated user.

**Response (200 OK):**
```json
{
    "message": "Logout successful"
}
```

---

### 📁 Project Endpoints

#### **GET** `/projects`
Retrieve a paginated list of projects for the authenticated user.

**Query Parameters:**
- `page` (optional): Page number for pagination

**Response (200 OK):**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Website Redesign",
            "client": "Acme Corp",
            "start_date": "2025-01-01",
            "end_date": "2025-03-31",
            "status": "in_progress",
            "user_id": 1,
            "created_at": "2025-12-01T10:00:00.000000Z",
            "updated_at": "2025-12-01T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 50
    }
}
```

---

#### **POST** `/projects`
Create a new project.

**Request Body:**
```json
{
    "title": "Website Redesign",
    "client": "Acme Corp",
    "start_date": "2025-01-01",
    "end_date": "2025-03-31",
    "status": "pending"
}
```

**Validation Rules:**
- `title`: required, string, max:255
- `client`: required, string, max:255
- `start_date`: required, date, must be today or later
- `end_date`: required, date, must be after start_date
- `status`: optional, enum: `pending`, `in_progress`, `completed`, `on_hold`

**Response (201 Created):**
```json
{
    "message": "Project created successfully",
    "data": {
        "id": 1,
        "title": "Website Redesign",
        "client": "Acme Corp",
        "start_date": "2025-01-01",
        "end_date": "2025-03-31",
        "status": "pending",
        "user_id": 1,
        "created_at": "2025-12-02T18:50:00.000000Z",
        "updated_at": "2025-12-02T18:50:00.000000Z"
    }
}
```

---

#### **GET** `/projects/{id}`
Retrieve a specific project with its tasks.

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "title": "Website Redesign",
        "client": "Acme Corp",
        "start_date": "2025-01-01",
        "end_date": "2025-03-31",
        "status": "in_progress",
        "user_id": 1,
        "tasks": [
            {
                "id": 1,
                "title": "Design Homepage",
                "deadline": "2025-01-15",
                "status": "in_progress",
                "assigned_user_id": 1,
                "project_id": 1
            }
        ],
        "created_at": "2025-12-01T10:00:00.000000Z",
        "updated_at": "2025-12-01T10:00:00.000000Z"
    }
}
```

---

### ✅ Task Endpoints

#### **POST** `/projects/{projectId}/tasks`
Create a new task within a project.

**Request Body:**
```json
{
    "title": "Design Homepage",
    "deadline": "2025-01-15",
    "assigned_user_id": 1,
    "status": "pending"
}
```

**Validation Rules:**
- `title`: required, string, max:255
- `deadline`: required, date, must be today or later
- `assigned_user_id`: optional, integer, must exist in users table (defaults to authenticated user)
- `status`: optional, enum: `pending`, `in_progress`, `completed`, `blocked`

**Response (201 Created):**
```json
{
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "title": "Design Homepage",
        "deadline": "2025-01-15",
        "status": "pending",
        "assigned_user_id": 1,
        "project_id": 1,
        "assigned_user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
        },
        "created_at": "2025-12-02T18:50:00.000000Z",
        "updated_at": "2025-12-02T18:50:00.000000Z"
    }
}
```

---

### 📮 Postman Collection

A complete Postman collection is included in the repository at `postman_collection.json`. Import this file into Postman for easy API testing.

**Features:**
- Pre-configured requests for all endpoints
- Automatic token management
- Example request/response bodies
- Environment variables for base URL

---

## 💻 Code Implementation Guide

### Service Layer Pattern

The **Service Layer** contains business logic and orchestrates operations between controllers and repositories.

**Example: ProjectService**

```php
namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function createProject(array $data): Project
    {
        // Business logic here (validation, transformation, etc.)
        return $this->projectRepository->create($data);
    }

    public function getProjectWithTasks(int $id): ?Project
    {
        return $this->projectRepository->findByIdWithTasks($id);
    }
}
```

**When to use:**
- Complex business logic
- Multiple repository interactions
- Job dispatching
- External API calls
- Data transformation

---

### Repository Layer Pattern

The **Repository Layer** abstracts database operations and provides a clean API for data access.

**Step 1: Define the Interface**

```php
namespace App\Repositories\Contracts;

interface ProjectRepositoryInterface
{
    public function create(array $data): Project;
    public function findById(int $id): ?Project;
    public function getAll(): Collection;
}
```

**Step 2: Implement the Repository**

```php
namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function findById(int $id): ?Project
    {
        return Project::find($id);
    }

    public function getAll(): Collection
    {
        return Project::with('tasks')->get();
    }
}
```

**Step 3: Bind in Service Provider**

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProjectRepositoryInterface::class,
            ProjectRepository::class
        );
    }
}
```

**Step 4: Register the Provider**

Add to `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

---

### Controller Layer

Controllers handle HTTP requests and delegate to services.

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProjectRequest;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    public function store(CreateProjectRequest $request): JsonResponse
    {
        // Authorization
        $this->authorize('create', Project::class);

        // Prepare data
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // Delegate to service
        $project = $this->projectService->createProject($data);

        // Return response
        return response()->json([
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project),
        ], Response::HTTP_CREATED);
    }
}
```

---

### Form Request Validation

Centralize validation logic in Form Request classes.

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:pending,in_progress,completed,on_hold',
        ];
    }
}
```

---

### Policy-Based Authorization

Define authorization logic in Policy classes.

```php
namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
```

**Register in AuthServiceProvider:**

```php
protected $policies = [
    Project::class => ProjectPolicy::class,
    Task::class => TaskPolicy::class,
];
```

---

### Queue Jobs

Background jobs for asynchronous processing.

```php
namespace App\Jobs;

use App\Mail\TaskAssignedNotification;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendTaskNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function handle(): void
    {
        Mail::to($this->task->assignedUser->email)
            ->send(new TaskAssignedNotification($this->task));
    }
}
```

**Dispatch the job:**

```php
SendTaskNotificationJob::dispatch($task);
```

---

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProjectTest.php

# Run with coverage
php artisan test --coverage
```

### Example Test

```php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class ProjectTest extends TestCase
{
    public function test_user_can_create_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/projects', [
            'title' => 'Test Project',
            'client' => 'Test Client',
            'start_date' => '2025-01-01',
            'end_date' => '2025-03-31',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data']);
    }
}
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

For questions or issues, please open an issue on GitHub or contact the maintainers.

---

**Built with ❤️ using Laravel and SOLID Principles**
