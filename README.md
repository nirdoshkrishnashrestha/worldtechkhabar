# World Tech Khabar

World Tech Khabar is a production-oriented AI and technology news website for `https://worldtechkhabar.com/`.

- `backend/`: Laravel API, admin dashboard, MySQL migrations, news fetch/scoring/publishing commands.
- `frontend/`: React + Vite + Tailwind static frontend for shared hosting.

The system collects metadata, short excerpts, and summaries from official/free sources, stores articles in MySQL, scores them, and publishes selected stories. It does not copy full copyrighted articles.

## Local Installation On MacBook

Install PHP 8.3+, Composer, Node.js, and MySQL with Homebrew or your preferred installer.

Backend:

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="World Tech Khabar"
APP_URL=https://worldtechkhabar.com
FRONTEND_URL=http://localhost:5173
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=worldtechkhabar
DB_USERNAME=root
DB_PASSWORD=
ADMIN_NAME="World Tech Khabar Admin"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=change-this-password
```

Run migrations and seeders:

```bash
php artisan migrate --seed
php artisan serve
```

Frontend:

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

For local frontend development, set:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

## Admin Login

If `ADMIN_EMAIL` and `ADMIN_PASSWORD` are set before `php artisan migrate --seed`, the seeder creates an admin user automatically.

To create or update an admin manually:

```bash
php artisan tinker
```

```php
\App\Models\User::updateOrCreate(
    ['email' => 'admin@example.com'],
    ['name' => 'Admin', 'password' => bcrypt('secure-password'), 'is_admin' => true]
);
```

Admin dashboard:

```text
/admin
```

## News Automation Commands

```bash
php artisan news:fetch
php artisan news:fetch-source 1
php artisan news:score
php artisan news:publish-auto
php artisan news:daily-digest
```

`news:publish-auto` also refreshes scores before applying publishing rules.

Default publishing rules:

- Content must contain at least 150 words or it is not accepted for publishing.
- Score `>= 35`: publish automatically.
- Score `< 35`: do not store the article.
- Duplicate published articles are not published again.

Scores are based on source trust, priority, AI/tech keywords, recency, metadata quality, duplicate similarity, age, and topical relevance.

## cPanel Cron Examples

Every 3 hours:

```bash
php /home/USERNAME/path-to-backend/artisan news:fetch
```

Every 3 hours, 15 minutes later:

```bash
php /home/USERNAME/path-to-backend/artisan news:publish-auto
```

Daily:

```bash
php /home/USERNAME/path-to-backend/artisan news:daily-digest
```

## Production Build

Frontend:

```bash
cd frontend
npm install
npm run build
```

Upload the contents of `frontend/dist/` to `public_html`.
The frontend includes an Apache `.htaccess` fallback so routes like `/news/article-slug` load the React app on shared hosting.

Backend:

```bash
cd backend
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Shared Hosting Deployment

1. Create a MySQL database and user in cPanel.
2. Upload `backend/` outside `public_html` if your host allows it.
3. Point a subdomain such as `api.worldtechkhabar.com` to `backend/public`, or configure your host so `/backend/public` serves Laravel.
4. Set `APP_URL`, `FRONTEND_URL`, and MySQL credentials in `backend/.env`.
5. Run `php artisan migrate --seed` from cPanel terminal or SSH.
6. Upload `frontend/dist/` files to `public_html`.
7. Set `frontend/.env` before building so `VITE_API_BASE_URL` points to the Laravel API, for example:

```env
VITE_API_BASE_URL=https://api.worldtechkhabar.com/api
```

8. Set file permissions so `backend/storage` and `backend/bootstrap/cache` are writable.
9. Add the cron jobs listed above in cPanel.

## Logo

Place your `logo.png` in:

```text
frontend/public/logo.png
```

After `npm run build`, it will be copied into the frontend static output. The header and footer expect `/logo.png`.

## Managing Sources

Go to `Admin > Sources` to add or edit official sources.

Supported automated source styles:

- RSS: `source_type=rss`, `feed_url` is the RSS/Atom URL.
- arXiv API: `source_type=api`, `feed_url` is `cs.AI`, `cs.LG`, `cs.CL`, or `cs.CV`.
- GitHub releases: `source_type=api`, `feed_url` is `owner/repo`, and `official_url` is the GitHub repository URL.
- Webpage: stored as an official metadata source. Add an RSS/API URL when available for automated fetching.

Use the “Test fetch” button in the dashboard to fetch one source manually.

## Legal Note

Use only official/free sources where fetching is allowed. World Tech Khabar stores titles, metadata, short summaries, short excerpts, dates, images when provided by feeds, and original links. Do not copy or republish full articles from source websites.

Every article page includes a “Read original source” button.
# worldtechkhabar
