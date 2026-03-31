# Laravel Task Management API — README

## Project Overview

This is a **Task Management API** built with **Laravel** and **MySQL**, designed for the Laravel Engineer Intern Take-Home Assignment.

It provides the following features:

1. Create tasks  
2. List tasks  
3. Update task status  
4. Delete tasks  
5. Bonus: Daily task report  

The API enforces rules such as **unique task titles per due date**, **status progression**, and **priority validation**.

---

## Table Structure

**Table: tasks**

| Column      | Type    | Description                     |
|------------|--------|---------------------------------|
| id         | integer | Primary key                     |
| title      | string  | Task title                      |
| due_date   | date    | Task deadline                  |
| priority   | enum    | low, medium, high               |
| status     | enum    | pending, in_progress, done      |
| created_at | timestamp | Laravel default               |
| updated_at | timestamp | Laravel default               |

---

## Installation & Setup

### 1. Clone the Repository
```bash
git clone <your-repo-url>
cd <project-folder>
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-scripts --no-interaction
```

### 3. Configure Environment
- Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
- Update database configuration for Railway MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=<RAILWAY_PRIVATE_DOMAIN>
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=<RAILWAY_DB_PASSWORD>
```

- Generate Laravel key:
```bash
php artisan key:generate
```

### 4. Run Migrations
```bash
php artisan migrate
```
This creates the `tasks` table in your MySQL database.

---

## Running Locally
```bash
php artisan serve
```
- Default URL: `http://127.0.0.1:8000`

---

## API Endpoints

### 1. Create Task
- **POST** `/api/tasks`  
- **Body (JSON):**
```json
{
  "title": "My First Task",
  "due_date": "2026-04-01",
  "priority": "high"
}
```
- **Rules:**
  - Title cannot duplicate for the same due date  
  - Priority: low, medium, high  
  - Due date must be today or later

---

### 2. List Tasks
- **GET** `/api/tasks`  
- **Optional Query:** `?status=pending`  
- Sorted by priority (high → low) and due_date ascending

---

### 3. Update Task Status
- **PATCH** `/api/tasks/{id}/status`  
- **Body (JSON):**
```json
{
  "status": "in_progress"
}
```
- **Rules:**  
  - Status must progress: pending → in_progress → done  
  - Cannot skip or revert

---

### 4. Delete Task
- **DELETE** `/api/tasks/{id}`  
- **Rules:**  
  - Only tasks with status `done` can be deleted

---

### 5. Daily Report (Bonus)
- **GET** `/api/tasks/report?date=YYYY-MM-DD`  
- Returns counts per priority and status for the given day

**Example Response:**
```json
{
  "date": "2026-04-01",
  "summary": {
    "high": {"pending": 2, "in_progress": 1, "done": 0},
    "medium": {"pending": 1, "in_progress": 0, "done": 3},
    "low": {"pending": 0, "in_progress": 0, "done": 1}
  }
}
```

---

## Testing API (Railway Hosted)

### 1. Using curl (Windows CMD)
- **Create Task**
```cmd
curl -X POST "https://laravel-engineer-intern-take-home-assignment-production.up.railway.app/api/tasks" -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"title\":\"My First Task\",\"due_date\":\"2026-04-01\",\"priority\":\"high\"}"
```
- **List Tasks**
```cmd
curl "https://laravel-engineer-intern-take-home-assignment-production.up.railway.app/api/tasks"
```
- **Update Status**
```cmd
curl -X PATCH "https://laravel-engineer-intern-take-home-assignment-production.up.railway.app/api/tasks/1/status" -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"status\":\"in_progress\"}"
```
- **Delete Task**
```cmd
curl -X DELETE "https://laravel-engineer-intern-take-home-assignment-production.up.railway.app/api/tasks/1" -H "Accept: application/json"
```
- **Daily Report**
```cmd
curl "https://laravel-engineer-intern-take-home-assignment-production.up.railway.app/api/tasks/report?date=2026-04-01"
```

### 2. Using Postman
- Import endpoints  
- Send requests with JSON body  
- Confirm responses match rules

---

## Deployment Instructions (Railway)

1. Push code to GitHub  
2. Connect Railway to repository  
3. Set **environment variables** in Railway dashboard  
4. Deploy project → Railway provides live URL  
5. Run **migrations** on Railway database

---

## Evaluation Criteria Covered
- Business rules correctly enforced  
- Laravel best practices (Eloquent, Validation, Migrations)  
- API readable, maintainable, and testable  
- Hosted online with MySQL for testing

