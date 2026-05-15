# Student Notes Application

A full-stack notes management application with a Laravel REST API backend and Vue 3 frontend. Create, view, filter, and archive personal notes with a clean, responsive UI.

## Project Structure

```
assesment/
├── student-notes-api/       # Laravel 12 REST API backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/NoteController.php
│   │   │   ├── Requests/StoreNoteRequest.php
│   │   │   └── Kernel.php
│   │   ├── Models/Note.php
│   │   └── Policies/NotePolicy.php
│   ├── database/
│   │   ├── migrations/
│   │   └── factories/
│   ├── routes/api.php
│   ├── tests/Feature/NoteControllerTest.php
│   └── ...
│
└── notes-ui/                # Vue 3 + Vite frontend
    ├── src/
    │   ├── App.vue
    │   ├── main.js
    │   ├── style.css
    │   └── components/
    ├── package.json
    └── vite.config.js
```

## Features

- **Create Notes** – Add new notes with title, content, and priority level (low, medium, high)
- **List Notes** – View all notes with server-side pagination (10 per page)
- **Filter by Priority** – Filter notes by priority level
- **Archive Notes** – Mark notes as archived to keep your list clean
- **Responsive Design** – Works on desktop and mobile devices
- **Real-time Feedback** – Toast notifications for successful actions
- **Paginated API** – Backend returns paginated results with metadata

## Prerequisites

- **PHP 8.2+** (for Laravel backend)
- **Composer** (PHP dependency manager)
- **Node.js 16+** (for Vue frontend)
- **npm** or **yarn** (JavaScript package manager)
- **XAMPP** or similar (MySQL/MariaDB database)

## Installation & Setup

### 1. Backend Setup (Laravel API)

Navigate to the backend directory:
```bash
cd student-notes-api
```

Install PHP dependencies:
```bash
composer install
```

Copy the environment file (if not already present):
```bash
copy .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Create the SQLite database file or configure MySQL in `.env`:
```env
DB_CONNECTION=sqlite
# or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=notes_app
# DB_USERNAME=root
# DB_PASSWORD=
```

Run database migrations:
```bash
php artisan migrate
```

*(Optional) Seed sample data:*
```bash
php artisan db:seed
```

### 2. Frontend Setup (Vue + Vite)

Navigate to the frontend directory:
```bash
cd ../notes-ui
```

Install JavaScript dependencies:
```bash
npm install
```

## Running the Application

### Start the Backend (Laravel API)

From the `student-notes-api` directory, start the development server:
```bash
php artisan serve
```

The API will be available at: **http://localhost:8000**

### Start the Frontend (Vue + Vite)

From the `notes-ui` directory, start the development server:
```bash
npm run dev
```

The frontend will be available at: **http://localhost:5173**

Open your browser and navigate to `http://localhost:5173` to use the application.

## API Endpoints

All endpoints are under `/api/` prefix.

### GET /api/notes
Retrieve all notes with pagination and filtering.

**Query Parameters:**
- `page` (optional, default: 1) – Page number
- `per_page` (optional, default: 10) – Items per page
- `priority` (optional) – Filter by priority: `low`, `medium`, or `high`

**Example:**
```bash
curl http://localhost:8000/api/notes?page=1&per_page=10&priority=high
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "My First Note",
      "content": "Note content here",
      "priority": "high",
      "is_archived": false,
      "created_at": "2026-05-15T10:30:00.000000Z",
      "updated_at": "2026-05-15T10:30:00.000000Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 5, "per_page": 10, "total": 50 }
}
```

### POST /api/notes
Create a new note.

**Request Body:**
```json
{
  "title": "Note Title",
  "content": "Note content here",
  "priority": "medium"
}
```

**Response (201 Created):**
```json
{
  "message": "Note created",
  "data": {
    "id": 1,
    "title": "Note Title",
    "content": "Note content here",
    "priority": "medium",
    "is_archived": false,
    "created_at": "2026-05-15T10:30:00.000000Z",
    "updated_at": "2026-05-15T10:30:00.000000Z"
  }
}
```

### PATCH /api/notes/{id}/archive
Archive a note by ID.

**Example:**
```bash
curl -X PATCH http://localhost:8000/api/notes/1/archive
```

**Response (200 OK):**
```json
{
  "message": "Note archived"
}
```

## Testing

### Backend Tests

Run all backend tests:
```bash
php artisan test
```

Run only NoteController tests:
```bash
php artisan test --filter NoteControllerTest
```

Run with verbose output:
```bash
php artisan test --testdox
```

**Test Coverage:**
- Create note with valid data
- Create note with validation errors
- Archive note successfully
- Archive non-existent note (404)
- List notes with pagination
- Filter notes by priority

### Frontend Tests

Frontend unit tests are not yet configured. To add them:

```bash
npm install --save-dev vitest @vue/test-utils jsdom
```

Then update `package.json` scripts:
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "test": "vitest"
  }
}
```

See [TESTING.md](./student-notes-api/TESTING.md) for more details.

## Using Postman

1. Open Postman and import requests:

**GET /api/notes**
- Method: GET
- URL: `http://localhost:8000/api/notes`
- Params: `page=1`, `per_page=10`

**POST /api/notes**
- Method: POST
- URL: `http://localhost:8000/api/notes`
- Body (JSON):
```json
{
  "title": "My Note",
  "content": "Content here",
  "priority": "high"
}
```

**PATCH /api/notes/{id}/archive**
- Method: PATCH
- URL: `http://localhost:8000/api/notes/1/archive`

## Using cURL

**List notes:**
```bash
curl http://localhost:8000/api/notes
```

**Create a note:**
```bash
curl -X POST http://localhost:8000/api/notes \
  -H "Content-Type: application/json" \
  -d '{"title":"My Note","content":"Content","priority":"medium"}'
```

**Archive a note:**
```bash
curl -X PATCH http://localhost:8000/api/notes/1/archive
```

## Troubleshooting

### CORS Errors
If you see CORS errors in the browser console:
- Ensure the backend is running on `http://localhost:8000`
- Check `config/cors.php` – it should allow all origins/methods for development
- The `HandleCors` middleware is registered in `app/Http/Kernel.php`

### "SQLSTATE[HY000]: General error: 1 no such table: notes"
Run migrations:
```bash
php artisan migrate
```

### Frontend can't connect to API
- Check that the backend is running: `php artisan serve`
- Verify the API URL in `notes-ui/src/App.vue` (should be `http://localhost:8000`)
- Check browser console for error messages

### "Cannot find module" errors in npm
Run:
```bash
npm install
```

## Architecture & Key Decisions

- **Server-side Pagination:** Notes are paginated on the backend using Eloquent's `paginate()` method
- **CORS Handling:** Built-in `HandleCors` middleware (no external packages)
- **Authorization:** `NotePolicy` class provides flexible permission checking
- **Validation:** Form requests (`StoreNoteRequest`) centralize input validation
- **Frontend State:** Vue's `ref()` for reactive notes, filters, and toast notifications

## Environment Configuration

Edit `student-notes-api/.env` to customize:

```env
APP_NAME="Student Notes"
APP_DEBUG=true
DB_CONNECTION=sqlite
CORS_ALLOWED_ORIGINS=*
```

## Development Notes

- Frontend hot reload is enabled by default via Vite
- Backend uses Laravel's artisan CLI for most tasks
- Database uses SQLite by default (configured in `.env`)
- API responses include pagination metadata under `meta` and `links` keys

## License

This project is provided as-is for educational purposes.
