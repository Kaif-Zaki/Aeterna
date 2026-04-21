# Aeterna PHP Website

## Project Structure
- Public pages at project root: `index.php`, `about.php`, `collections.php`, `gallery.php`, `contact.php`, `faq.php`
- Admin pages: `admin/`
- API: `api/`
- DB migration: `database/migrate.php`
- SQL schema: `sql/schema.sql`
- Environment file: `.env`

## Run Locally
```bash
php -S 127.0.0.1:8000
```

Open:
- `http://127.0.0.1:8000/`

## Run Migration
```bash
php database/migrate.php
```
