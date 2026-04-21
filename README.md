## Project setup

### Install dependencies

```bash
composer install
```

### Environment configuration

Clone the .env.example file to .env file and add the configuration.

```bash
cp .env.example .env
```

### Start the development server

```bash
composer run dev
```

### Running Tests

Execute the test suite using:

```bash
php artisan test
```

## API Documentation

### Endpoint

POST /api/employee/process

### Full URL

http://localhost:8000/api/employee/process

### Request Parameters

| Parameter          | Type | Description                   |
| ------------------ | ---- | ----------------------------- |
| previous_year_file | File | CSV file of previous year     |
| employees_file     | File | CSV file of current employees |

### Description

This endpoint processes employee data and generates a Secret Santa assignment CSV, ensuring:

No employee is assigned to themselves

No duplicate assignments

Previous year pairings are avoided

### Response

Success: Returns a downloadable CSV file (secret_santa.csv)

Error: Returns a JSON response with error details and HTTP status 500
