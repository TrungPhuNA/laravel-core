# Hướng dẫn Setup Môi trường Build & Deploy (DevOps)

Tài liệu này cung cấp các thông số kỹ thuật và hướng dẫn cài đặt dự án Laravel Core cho đội ngũ DevOps.

## 1. Tech Stack & Versions

Bắt buộc cài đặt các phiên bản sau hoặc cao hơn để đảm bảo tính tương thích:

| Thành phần | Phiên bản yêu cầu | Ghi chú |
| :--- | :--- | :--- |
| **PHP** | `^8.2` | Yêu cầu các extension: `bcmath`, `ctype`, `fileinfo`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `gd`, `zip` |
| **Node.js** | `^20.x` | Cần thiết để build Tailwind CSS v4 và Vite |
| **Composer** | `^2.x` | Quản lý package PHP |
| **MySQL** | `^8.0` | Hệ quản trị cơ sở dữ liệu chính |
| **Nginx** | `^1.20` | Web Server |
| **Redis** | `^7.x` | Tùy chọn cho Caching & Queue (khuyên dùng) |

---

## 2. Cài đặt Hệ thống (Ubuntu/Debian Example)

### PHP & Extensions
```bash
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-xml php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl
```

### Node.js (via NodeSource)
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 3. Các bước Triển khai (Deployment Steps)

### Bước 1: Clone source code và cài đặt Dependencies
```bash
# Clone dự án
git clone <repository_url>
cd laravel-core

# Cài đặt PHP dependencies
composer install --optimize-autoloader --no-dev

# Cài đặt Node dependencies và build assets
npm install
npm run build
```

### Bước 2: Cấu hình Environment (`.env`)
Sao chép file mẫu và cấu hình thông số MySQL:
```bash
cp .env.example .env
```
Cập nhật các thông số quan trọng trong `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Cấu hình Database MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cấu hình Cache/Queue (nếu dùng Redis)
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### Bước 3: Khởi tạo Project
```bash
# Tạo Key ứng dụng
php artisan key:generate --force

# Chạy Migration
php artisan migrate --force

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Phân quyền (File Permissions)
Đảm bảo Nginx/PHP-FPM (thường là user `www-data`) có quyền ghi vào các thư mục sau:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 5. Cấu hình Nginx (Nginx Configuration)
Mẫu cấu hình tối ưu cho Laravel tại `/etc/nginx/sites-available/laravel-core`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/laravel-core/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Optimize for Static Assets (Vite build)
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf)$ {
        expires 1y;
        add_header Cache-Control "public, no-transform";
        access_log off;
    }

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
}
```

---

## 6. Cấu hình Queue (Supervisor) - Tùy chọn
Nếu dự án sử dụng Queue, cần cấu hình Supervisor để duy trì worker:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-core/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laravel-core/storage/logs/worker.log
stopwaitsecs=3600
```

---
*Tài liệu được cập nhật vào: 13/04/2026*
