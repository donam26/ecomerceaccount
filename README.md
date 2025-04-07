<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Website Bán Tài Khoản Game Liên Quân

Website bán tài khoản game Liên Quân được xây dựng bằng Laravel và Tailwind CSS.

## Hướng dẫn cài đặt

1. Clone dự án
   ```bash
   git clone <repository_url>
   cd ecommerceaccount
   ```

2. Cài đặt các dependencies
   ```bash
   composer install
   npm install
   ```

3. Cấu hình môi trường
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Thiết lập cơ sở dữ liệu
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. Biên dịch assets
   ```bash
   npm run dev
   ```

6. Chạy ứng dụng
   ```bash
   php artisan serve
   ```

## Thiết lập Cổng Thanh Toán SePay

### 1. Cài đặt Package

```bash
composer require sepayvn/laravel-sepay
```

### 2. Xuất bản file cấu hình

```bash
php artisan vendor:publish --tag="sepay-config"
```

### 3. Cấu hình trong file .env

```
SEPAY_WEBHOOK_TOKEN=your_api_key_here
SEPAY_MATCH_PATTERN=SE
```

### 4. Cấu hình Webhook trong trang quản lý SePay

1. Đăng nhập vào trang quản lý SePay
2. Truy cập mục Webhooks
3. Bấm nút "Thêm Webhook"
4. Điền các thông tin như sau:
   - URL: `https://your-domain.com/api/sepay/webhook`
   - Kiểu chứng thực: API Key
   - API Key: Nhập một khóa bí mật ngẫu nhiên (giống với giá trị SEPAY_WEBHOOK_TOKEN trong file .env)
5. Lưu lại

### 5. Kiểm tra với Postman

Để kiểm tra webhook đã hoạt động đúng, bạn có thể gửi một request mẫu qua Postman:

```
POST https://your-domain.com/api/sepay/webhook
Header: Authorization: Bearer your_api_key_here
Content-Type: application/json

{
    "gateway": "MBBank",
    "transactionDate": "2024-05-25 21:11:02",
    "accountNumber": "0359123456",
    "subAccount": null,
    "code": null,
    "content": "Thanh toan QR SE123456",
    "transferType": "in",
    "description": "Thanh toan QR SE123456",
    "transferAmount": 1700000,
    "referenceCode": "FT123456789",
    "accumulated": 0,
    "id": 123456
}
```

## Chức năng chính

- Đăng nhập/Đăng ký
- Xem danh sách game
- Xem danh sách tài khoản
- Tìm kiếm và lọc tài khoản
- Mua tài khoản
- Thanh toán qua SePay
- Quản lý đơn hàng
- Bảng điều khiển admin
