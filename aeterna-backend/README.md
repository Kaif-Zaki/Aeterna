# Aeterna Backend — Node.js + Express + MySQL

## Setup

1. **Install dependencies**
   ```bash
   npm install
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your MySQL credentials
   ```

4. **Run**
   ```bash
   npm run dev   # development (auto-restart)
   npm start     # production
   ```

## API Endpoints

| Method | Route            | Description              |
|--------|------------------|--------------------------|
| POST   | `/api/contact`   | Submit a contact message |
| GET    | `/api/contact`   | List all messages (admin)|
| GET    | `/api/health`    | Health check             |

### POST /api/contact

```json
{ "name": "Jane", "email": "jane@example.com", "message": "Hello!" }
```

Response `201`:
```json
{ "success": true, "id": 1 }
```

### Connecting the Frontend

Update your React `ContactPage` fetch URL to `http://localhost:5000/api/contact`.
