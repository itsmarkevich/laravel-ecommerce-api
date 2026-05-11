# Laravel E-commerce API

Backend API для pet-проекта интернет-магазина пиццы и напитков.

## Стек

- PHP 8.2+
- Laravel 12
- PostgreSQL
- Docker / Docker Compose
- JWT Auth (`tymon/jwt-auth`)
- GitLab CI
- PHP CS Fixer
- PHPStan / Larastan
- Rector
- PHPUnit Feature Tests

## Что реализовано

- Публичное меню товаров и категорий
- Админский CRUD категорий и продуктов
- Авторизация по телефону через SMS-код
- JWT access token, refresh и logout
- Роли пользователей: `user`, `admin`
- Корзина пользователя
- Ограничения корзины: максимум 10 пицц и 20 напитков
- Создание заказа из корзины
- Валидация адреса доставки
- Snapshot товаров в заказе через `product_name` и `product_price`
- Feature-тесты для основных API-сценариев
- CI pipeline с проверками CS Fixer, PHPStan, Rector и тестами

## Планируемые доработки

- Кеширование списка продуктов в Redis
- Асинхронная генерация отчётов через RabbitMQ
- События приложения и локализация через Events + i18n
- Расширение API админ-панели
- Профилирование и отладка через Laravel Telescope
- Расширяемые характеристики товаров
- Документация API

## Запуск проекта

### Требования

Перед запуском должны быть установлены:

- Docker
- Docker Compose
- Git

### 1. Клонировать репозиторий

```bash
git clone <repository-url>
cd laravel-ecommerce-api
```

### 2. Создать `.env`

```bash
cp .env.example .env
```

### 3. Поднять контейнеры

```bash
docker compose up -d --build
```

### 4. Установить зависимости Composer

```bash
docker compose exec app composer install
```

### 5. Сгенерировать Laravel app key

```bash
docker compose exec app php artisan key:generate
```

### 6. Сгенерировать JWT secret

```bash
docker compose exec app php artisan jwt:secret
```

### 7. Запустить миграции

```bash
docker compose exec app php artisan migrate
```

### 8. Запустить сидеры

```bash
docker compose exec app php artisan db:seed
```

Либо можно пересоздать базу и сразу наполнить её демо-данными:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

### 9. Проверить доступность приложения

API доступно по адресу:

```text
http://localhost:8080/api/v1
```

Adminer доступен по адресу:

```text
http://localhost:8081
```

## Тестовые пользователи

После запуска сидеров доступны демо-пользователи.

Обычный пользователь:

```text
phone: +79990000001
email: user@example.com
role: user
```

Администратор:

```text
phone: +79990000002
email: admin@example.com
role: admin
```

## Авторизация

Авторизация работает через SMS-код. В local-окружении SMS не отправляется реально, а записывается в лог.

Отправить код:

```http
POST http://localhost:8080/api/v1/auth/send-code
```

Body:

```json
{
  "phone": "+79990000001"
}
```

Посмотреть код можно в SMS-логе:

```bash
docker compose exec app tail -f storage/logs/sms.log
```

Если отдельный `sms.log` отсутствует, проверь общий Laravel-лог:

```bash
docker compose exec app tail -f storage/logs/laravel.log
```

Подтвердить код:

```http
POST http://localhost:8080/api/v1/auth/verify-code
```

Body:

```json
{
  "phone": "+79990000001",
  "code": 123456
}
```

В ответе будет `access_token`. Для защищённых роутов нужно передавать его в заголовке:

```http
Authorization: Bearer <access_token>
```

## Проверки качества кода

Запуск тестов:

```bash
docker compose exec app composer test
```

Проверка PHP CS Fixer:

```bash
docker compose exec app composer cs:check
```

Проверка PHPStan:

```bash
docker compose exec app composer stan
```

Проверка Rector:

```bash
docker compose exec app composer rector:check
```
