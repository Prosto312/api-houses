- Dev: uzenov.ilyas@gmail.com
- WebSite: uzenov.dev
- Location: Kyrgyzstan, Bishkek


# PHP Developer Test

## Background

Demonstrate your skills using a varied range of technologies our company commonly uses.

We’ll be assessing **task completeness** as well as **code quality**.

When finished:

- Publish the project to **GitHub**
- Include any instructions, notes, or parts you wish to highlight
- Feel free to demonstrate any additional knowledge or skills where applicable

---

## Estimated Time

⏱ **3 hours**

---

## Skills Tested

### Primary

- **PHP 8.4 (Laravel v12)**
- **Vue.js**
- **HTML**
- **Git Workflow**
    - Commit your work to a local Git repository within your working folder as you finish logical parts of the task.
    - Include the `.git` folder.
    - Our company follows a **commit early / commit often** mantra.

### Bonus (Optional)

- Use **Element UI** to style the frontend section.

---

# Tasks

---

## API

Using the provided CSV data:

1. Convert the CSV into a **database table**
2. Provide:
    - Laravel **migrations**
    - Laravel **seeders**
3. Create an API route using Laravel that allows the data to be searched.

---

### Search Requirements

The API should support searching by the following fields:

- **Name**
    - Must match partial names (e.g., "Vic" → "Victoria House")

- **Bedrooms**
    - Exact match

- **Bathrooms**
    - Exact match

- **Storeys**
    - Exact match

- **Garages**
    - Exact match

- **Price**
    - Range search between **$X and $Y**

---

### Optional Parameters

All search parameters must be optional.

Examples:

- Search for **2-bedroom houses**
- Search for **4-bedroom and 2-bathroom houses**
- Search by **name only**
- Search by **price range only**

---

### API Response Format

- Must return **JSON**
- Must contain **pure numeric data**
- No HTML content should be included

---

## Frontend (Search Form)

Create a basic frontend search form that:

1. Queries the API using **AJAX**
2. Displays results dynamically in an HTML table
3. Uses **reactive Vue.js rendering**

---

### UI Requirements

- Include a search/loading indicator  
  (e.g., a spinning icon)

- Display a message when no results are found

Example:

> "No houses match your search criteria."

---

## Deliverables

- Laravel project with:
    - Migration + Seeder for CSV data
    - Search API endpoint
    - Vue.js frontend search form
- Git commit history showing progress
- Published GitHub repository with setup instructions

---

## Docker

Start services:

```bash
docker compose up -d
```

Start services with Kibana:

```bash
docker compose --profile kibana up -d
```

Stop services:

```bash
docker compose down
```

## Database

Run migrations and seed data:

```bash
php artisan migrate --seed
```

## Setup

Copy env and generate key:

```bash
cp .env.example .env
php artisan key:generate
```

Install dependencies:

```bash
composer install
npm install
```

## Run

Start backend:

```bash
php artisan serve
```

Start frontend:

```bash
npm run dev
```

## API

Search endpoint:

```bash
GET /api/houses
```

Query params:

```text
name
bedrooms
bathrooms
storeys
garages
price_from
price_to
page
per_page
```

Example:

```bash
curl "http://127.0.0.1:8000/api/houses?name=Vic&bedrooms=4&price_from=300000&price_to=600000"
```

## Search Drivers

Database search (default):

```env
SEARCH_DRIVER=database
```

Elasticsearch search:

```env
SEARCH_DRIVER=elasticsearch
ELASTICSEARCH_HOST=http://localhost:9200
ELASTICSEARCH_INDEX=houses
```

Reindex:

```bash
php artisan houses:reindex
```

## Redis Cache

Cache store:

```env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
```

## Frontend

Open:

```text
http://127.0.0.1:8000
```
