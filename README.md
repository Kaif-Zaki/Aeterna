# Aeterna — Timeless Fashion Brand

> A full-stack luxury fashion brand web application built with React, Node.js, Express, and MySQL.

![Aeterna Banner](./aeterna-frontend/public/Aeterna2.png)

---

## 🌐 Live Preview

| Service      | URL                              |
| ------------ | -------------------------------- |
| Frontend     | http://localhost:8080            |
| Backend API  | http://localhost:3001            |
| Health Check | http://localhost:3001/api/health |

---

## ✨ Features

- 🖤 Luxury fashion brand UI with dark aesthetic
- 📱 Fully responsive — mobile, tablet, desktop
- 💌 Contact form with real-time validation & MySQL persistence
- 🔒 Secure backend with Helmet, CORS, and rate limiting
- ⚡ Blazing fast with Vite + React + TypeScript
- 🎨 Tailwind CSS with custom animations
- 🗄️ MySQL database with connection pooling

---

## 🗂️ Project Structure

```
untitled folder/
├── aeterna-frontend/          # React + Vite + TypeScript frontend
│   ├── public/                # Static assets (logo, images)
│   ├── src/
│   │   ├── assets/            # Images, fonts
│   │   ├── components/        # Reusable UI components
│   │   ├── hooks/             # Custom React hooks
│   │   ├── lib/               # Utility functions
│   │   ├── pages/             # Page components
│   │   ├── test/              # Unit tests
│   │   ├── App.tsx            # Root app component
│   │   └── main.tsx           # Entry point
│   ├── index.html
│   ├── tailwind.config.ts
│   ├── vite.config.ts
│   └── package.json
│
└── aeterna-backend/           # Node.js + Express backend
    ├── sql/
    │   └── schema.sql         # Database schema
    ├── src/
    │   ├── db/
    │   │   └── connection.js  # MySQL connection pool
    │   ├── routes/
    │   │   └── contact.js     # Contact API route
    │   └── index.js           # Express server entry point
    ├── .env                   # Environment variables
    └── package.json
```

---

## 🛠️ Tech Stack

### Frontend

| Technology          | Purpose                 |
| ------------------- | ----------------------- |
| React 18            | UI framework            |
| TypeScript          | Type safety             |
| Vite                | Build tool & dev server |
| Tailwind CSS        | Styling                 |
| tailwindcss-animate | Animations              |
| Zod                 | Form validation         |
| Sonner              | Toast notifications     |
| Lucide React        | Icons                   |

### Backend

| Technology         | Purpose               |
| ------------------ | --------------------- |
| Node.js            | Runtime               |
| Express.js         | Web framework         |
| MySQL2             | Database driver       |
| dotenv             | Environment config    |
| Helmet             | Security headers      |
| CORS               | Cross-origin requests |
| express-rate-limit | API rate limiting     |
| Nodemon            | Dev auto-restart      |

---

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- [Node.js](https://nodejs.org/) v18+
- [npm](https://www.npmjs.com/) v9+
- [MySQL](https://www.mysql.com/) v8+
- [Homebrew](https://brew.sh/) (macOS only)

---

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/aeterna.git
cd aeterna
```

---

### 2. Set Up MySQL Database

**Install and start MySQL (macOS):**

```bash
brew install mysql
brew services start mysql
```

**Create the database:**

```bash
mysql -u root -p
```

```sql
CREATE DATABASE IF NOT EXISTS aeterna;
EXIT;
```

**Run the schema:**

```bash
mysql -u root -p aeterna < aeterna-backend/sql/schema.sql
```

---

### 3. Configure Backend Environment

Create `aeterna-backend/.env`:

```env
PORT=3001
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=your_mysql_password
DB_NAME=aeterna
CORS_ORIGIN=http://localhost:8080
```

---

### 4. Install & Run Backend

```bash
cd aeterna-backend
npm install
npm run dev
```

You should see:

```
Aeterna API running on http://localhost:3001
```

---

### 5. Configure Frontend Environment

Create `aeterna-frontend/.env`:

```env
VITE_API_URL=http://localhost:3001
```

---

### 6. Install & Run Frontend

```bash
cd aeterna-frontend
npm install
npm run dev
```

You should see:

```
VITE v5.x.x  ready in 438ms
➜  Local:   http://localhost:8080/
```

---

## 📡 API Reference

### Base URL

```
http://localhost:3001/api
```

### Endpoints

#### `GET /health`

Health check endpoint.

**Response:**

```json
{ "status": "ok" }
```

---

#### `POST /contact`

Submit a contact form message.

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "message": "Hello, I'd like to know more about your collection."
}
```

**Success Response `201`:**

```json
{
  "success": true,
  "id": 1
}
```

**Validation Error `400`:**

```json
{
  "success": false,
  "errors": ["A valid email address is required."]
}
```

**Rate Limit `429`:**

```json
{
  "success": false,
  "errors": ["Too many requests."]
}
```

> ⚠️ Rate limited to **20 requests per 15 minutes** per IP.

---

## 🧪 Testing the API

```bash
curl -X POST http://localhost:3001/api/contact \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@test.com","message":"Hello!"}'
```

**Verify data in database:**

```bash
mysql -u root -p aeterna -e "SELECT * FROM contacts;"
```

---

## 🔐 Security

- **Helmet.js** — Sets secure HTTP headers
- **CORS** — Restricted to frontend origin only
- **Rate Limiting** — 20 requests/15 min on contact endpoint
- **Input Validation** — Zod on frontend, server-side validation on backend
- **Environment Variables** — All secrets in `.env` (never committed)

---

## 📦 Available Scripts

### Frontend (`aeterna-frontend/`)

| Command           | Description              |
| ----------------- | ------------------------ |
| `npm run dev`     | Start development server |
| `npm run build`   | Build for production     |
| `npm run preview` | Preview production build |
| `npm run test`    | Run unit tests           |
| `npm run lint`    | Lint code with ESLint    |

### Backend (`aeterna-backend/`)

| Command       | Description                       |
| ------------- | --------------------------------- |
| `npm run dev` | Start with nodemon (auto-restart) |
| `npm start`   | Start production server           |

---

## 🐛 Common Issues & Fixes

### `vite: command not found`

```bash
npm install
npm run dev
```

### `Error: Cannot find module 'dotenv'`

```bash
npm install
```

### `EADDRINUSE: address already in use :::5000`

Port 5000 is used by macOS AirPlay. Either:

- Disable **AirPlay Receiver** in System Settings → General → AirDrop & Handoff
- Or use port `3001` in your `.env`

### `EADDRINUSE: address already in use :::3001`

```bash
lsof -ti:3001 | xargs kill -9
npm run dev
```

### CORS errors in browser

Make sure `CORS_ORIGIN` in your backend `.env` matches exactly:

```env
CORS_ORIGIN=http://localhost:8080
```

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Kaif Zaki**

- GitHub: [@kaifzaki](https://github.com/Kaif-Zaki)

---

<p align="center">Built with ❤️ by Kaif Zaki — Aeterna © 2026</p>
