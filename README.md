# Task Management System

## Setup
1. Install dependencies: `composer install`.
2. Build Sail: `./vendor/bin/sail build`.
3. Run Sail: `./vendor/bin/sail up`.
4. Run migrations: `/vendor/bin/sailmigrate`.
5. (Optional) Seed database: `/vendor/bin/sail db:seed`.


## Testing
### Run `php artisan test` for unit and integration tests.

### POST [/api/auth/register](http://localhost:80/api/auth/register)
```
{
  "name": "John Doe",
  "email": "johndoe@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### POST [/api/auth/login](http://localhost:80/api/auth/login)
```
{
    "email": "johndoe@example.com",
    "password": "password123"
}
```

### GET [/api/tasks](http://localhost:80/api/tasks)

### POST [/api/tasks](http://localhost:80/api/tasks)
```
{
    "title": "New Task",
    "description": "This is a new task.",
    "status": "Pending",
    "priority": "High",
    "due_date": "2024-12-31"
}
```

### PUT [/api/tasks/{id}](http://localhost:80/api/tasks/1)
```
{
    "title": "Updated Task",
    "description": "Updated task description.",
    "status": "Completed",
    "priority": "Low",
    "due_date": "2024-12-20"
}
```

### DELETE [/api/tasks/{id}](http://localhost:80/api/tasks/1)

### POST /api/tasks/bulk-complete
```
{
    "task_ids": [1, 2, 3]
}
```

### GET [/api/categories](http://localhost:80/api/categories)

### POST [/api/categories](http://localhost:80/api/categories)
```
{
    "name": "New Category"
}
```
