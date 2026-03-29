# 🔒 ЧЕК-ЛИСТ БЕЗОПАСНОСТИ ПАНЕЛИ АДМИНИСТРАТОРА (ADMIN)

**Уровень доступа:** Admin (Level 80)  
**Дата создания:** 29 марта 2026 г.  
**Статус:** ✅ Реализовано

---

## 📋 СОДЕРЖАНИЕ

1. [Реализованные меры защиты](#1-реализованные-меры-защиты)
2. [Тесты на обход авторизации](#2-тесты-на-обход-авторизации)
3. [Тесты на эскалацию привилегий](#3-тесты-на-эскалацию-привилегий)
4. [Тесты на SQL-инъекции](#4-тесты-на-sql-инъекции)
5. [Тесты на XSS уязвимости](#5-тесты-на-xss-уязвимости)
6. [Тесты на CSRF](#6-тесты-на-csrf)
7. [Тесты Rate Limiting](#7-тесты-rate-limiting)
8. [Тесты Audit Log](#8-тесты-audit-log)
9. [Инструкция по запуску тестов](#9-инструкция-по-запуску-тестов)

---

## 1. РЕАЛИЗОВАННЫЕ МЕРЫ ЗАЩИТЫ

### 1.1 Middleware защиты

| Middleware | Файл | Описание |
|------------|------|----------|
| `auth` | Laravel | Проверка аутентификации |
| `role:admin` | `RoleMiddleware.php` | Проверка роли admin |
| `ensure.admin` | `EnsureAdminRole.php` | Строгая проверка админа |
| `session.activity` | `SessionActivity.php` | Контроль активности сессии |
| `2fa.verified` | `RequireTwoFactorAuthentication.php` | Проверка 2FA |

### 1.2 Логирование событий

| Событие | Константа | Описание |
|---------|-----------|----------|
| `admin.access` | `EVENT_ADMIN_ACCESS` | Доступ к панели админа |
| `admin.product.edit` | `EVENT_ADMIN_PRODUCT_EDIT` | Редактирование товара |
| `admin.product.delete` | `EVENT_ADMIN_PRODUCT_DELETE` | Удаление товара |
| `admin.user.create` | `EVENT_ADMIN_USER_CREATE` | Создание пользователя |
| `admin.user.delete` | `EVENT_ADMIN_USER_DELETE` | Удаление пользователя |
| `admin.customer.coupon` | `EVENT_ADMIN_CUSTOMER_COUPON` | Создание купона клиенту |
| `admin.shop.manage` | `EVENT_ADMIN_SHOP_MANAGE` | Управление магазином |

### 1.3 Rate Limiting

| Действие | Лимит | Период |
|----------|-------|--------|
| Удаление товаров | 10 | в минуту |
| Создание пользователей | 5 | в минуту |
| Изменение цен | 20 | в минуту |
| Экспорт данных | 3 | в минуту |
| Создание купонов | 15 | в минуту |
| Управление магазинами | 10 | в минуту |

### 1.4 Audit Log

Таблица: `admin_audit_logs`

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | bigint | ID записи |
| `user_id` | bigint | ID администратора |
| `action` | string | Тип действия |
| `model_type` | string | Тип модели |
| `model_id` | bigint | ID модели |
| `old_values` | json | Старые значения |
| `new_values` | json | Новые значения |
| `ip_address` | string | IP адрес |
| `user_agent` | text | User Agent |
| `created_at` | timestamp | Дата создания |

---

## 2. ТЕСТЫ НА ОБХОД АВТОРИЗАЦИИ

### Тест 2.1: Прямой доступ к /admin без авторизации

```bash
curl -X GET http://localhost/admin
```

**Ожидаемый результат:** Редирект на `/login`  
**Статус:** ⬜ Не протестировано

### Тест 2.2: Доступ с ролью seller

```bash
# Войти как seller и попробовать перейти на /admin
curl -X GET http://localhost/admin \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Ошибка 403 Forbidden  
**Статус:** ⬜ Не протестировано

### Тест 2.3: Доступ с ролью warehouse

```bash
# Войти как warehouse и попробовать перейти на /admin
```

**Ожидаемый результат:** Ошибка 403 Forbidden  
**Статус:** ⬜ Не протестировано

### Тест 2.4: Доступ с ролью client

```bash
# Войти как client и попробовать перейти на /admin
```

**Ожидаемый результат:** Ошибка 403 Forbidden  
**Статус:** ⬜ Не протестировано

### Тест 2.5: Подделка session cookie

```bash
curl -X GET http://localhost/admin \
  -H "Cookie: laravel_session=fake_session_id"
```

**Ожидаемый результат:** Ошибка 401/403 или редирект на login  
**Статус:** ⬜ Не протестировано

---

## 3. ТЕСТЫ НА ЭСКАЛАЦИЮ ПРИВИЛЕГИЙ

### Тест 3.1: Попытка установки role_id = 1 (owner)

```bash
curl -X POST http://localhost/admin/users/store \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"username":"test","email":"test@test.com","password":"123456","role_id":1}'
```

**Ожидаемый результат:** Валидация отклоняет role_id=1 или middleware блокирует  
**Статус:** ⬜ Не протестировано

### Тест 3.2: Попытка изменения своего role_id

```bash
curl -X PUT http://localhost/admin/users/update/ADMIN_ID \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"role_id":1}'
```

**Ожидаемый результат:** Ошибка 403 Forbidden  
**Статус:** ⬜ Не протестировано

### Тест 3.3: Доступ к маршрутам owner

```bash
curl -X GET http://localhost/owner/users \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Ошибка 403 Forbidden  
**Статус:** ⬜ Не протестировано

### Тест 3.4: Доступ к финансовым данным owner

```bash
curl -X GET http://localhost/owner/finance/profit \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Ошибка 403 Forbidden  
**Статус:** ⬜ Не протестировано

---

## 4. ТЕСТЫ НА SQL-ИНЪЕКЦИИ

### Тест 4.1: SQL-инъекция в поиске товаров

```bash
curl -X GET "http://localhost/admin/products?search=' OR '1'='1" \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Корректная обработка как строки  
**Статус:** ⬜ Не протестировано

### Тест 4.2: SQL-инъекция в поиске пользователей

```bash
curl -X GET "http://localhost/admin/users?search=' UNION SELECT * FROM users--" \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Корректная обработка как строки  
**Статус:** ⬜ Не протестировано

### Тест 4.3: SQL-инъекция в фильтре по дате

```bash
curl -X GET "http://localhost/admin/audit-log?date_from=2026-01-01' OR '1'='1" \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Корректная обработка как строки  
**Статус:** ⬜ Не протестировано

### Тест 4.4: SQL-инъекция в ID товара

```bash
curl -X GET "http://localhost/admin/products/1 OR 1=1/edit" \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Ошибка 404 или валидация  
**Статус:** ⬜ Не протестировано

---

## 5. ТЕСТЫ НА XSS УЯЗВИМОСТИ

### Тест 5.1: XSS в названии товара

```bash
curl -X POST http://localhost/admin/products/store \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"name":"<script>alert(1)</script>","article":"TEST","retail_price":100}'
```

**Ожидаемый результат:** Скрипт не выполняется, название экранировано  
**Статус:** ⬜ Не протестировано

### Тест 5.2: XSS в имени пользователя

```bash
curl -X POST http://localhost/admin/users/store \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"username":"<img src=x onerror=alert(1)>","email":"test@test.com","password":"123456","role_id":3}'
```

**Ожидаемый результат:** Скрипт не выполняется  
**Статус:** ⬜ Не протестировано

### Тест 5.3: XSS в описании товара

```bash
curl -X POST http://localhost/admin/products/store \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"name":"Test","article":"TEST","description":"<script>alert(document.cookie)</script>","retail_price":100}'
```

**Ожидаемый результат:** Скрипт не выполняется  
**Статус:** ⬜ Не протестировано

---

## 6. ТЕСТЫ НА CSRF

### Тест 6.1: Удаление товара без CSRF токена

```bash
curl -X POST http://localhost/admin/products/delete/PRODUCT_ID \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Ошибка 419 CSRF Token Mismatch  
**Статус:** ⬜ Не протестировано

### Тест 6.2: Создание пользователя без CSRF токена

```bash
curl -X POST http://localhost/admin/users/store \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"username":"test","email":"test@test.com","password":"123456","role_id":3}'
```

**Ожидаемый результат:** Ошибка 419 CSRF Token Mismatch  
**Статус:** ⬜ Не протестировано

### Тест 6.3: Изменение настроек без CSRF токена

```bash
curl -X POST http://localhost/admin/settings/save \
  -H "Cookie: laravel_session=..."
```

**Ожидаемый результат:** Ошибка 419 CSRF Token Mismatch  
**Статус:** ⬜ Не протестировано

---

## 7. ТЕСТЫ RATE LIMITING

### Тест 7.1: Превышение лимита удаления товаров

```bash
# Выполнить 15 запросов удаления подряд
for i in {1..15}; do
  curl -X POST http://localhost/admin/products/delete/$PRODUCT_ID \
    -H "Cookie: laravel_session=..."
done
```

**Ожидаемый результат:** После 10 запросов ошибка 429 Too Many Requests  
**Статус:** ⬜ Не протестировано

### Тест 7.2: Превышение лимита создания пользователей

```bash
# Выполнить 10 запросов создания подряд
for i in {1..10}; do
  curl -X POST http://localhost/admin/users/store \
    -H "Content-Type: application/json" \
    -H "Cookie: laravel_session=..." \
    -d "{\"username\":\"test$i\",\"email\":\"test$i@test.com\",\"password\":\"123456\",\"role_id\":3}"
done
```

**Ожидаемый результат:** После 5 запросов ошибка 429 Too Many Requests  
**Статус:** ⬜ Не протестировано

### Тест 7.3: Превышение лимита изменения цен

```bash
# Выполнить 25 запросов изменения цены
for i in {1..25}; do
  curl -X POST http://localhost/admin/products/update/$PRODUCT_ID \
    -H "Content-Type: application/json" \
    -H "Cookie: laravel_session=..." \
    -d '{"retail_price":999}'
done
```

**Ожидаемый результат:** После 20 запросов ошибка 429 Too Many Requests  
**Статус:** ⬜ Не протестировано

---

## 8. ТЕСТЫ AUDIT LOG

### Тест 8.1: Проверка записи создания товара

```bash
# 1. Создать товар
# 2. Проверить таблицу admin_audit_logs
SELECT * FROM admin_audit_logs 
WHERE action = 'product.create' 
ORDER BY created_at DESC LIMIT 1;
```

**Ожидаемый результат:** Запись с action='product.create', user_id, model_type='App\Models\Product'  
**Статус:** ⬜ Не протестировано

### Тест 8.2: Проверка записи удаления пользователя

```bash
# 1. Удалить пользователя
# 2. Проверить таблицу admin_audit_logs
SELECT * FROM admin_audit_logs 
WHERE action = 'user.delete' 
ORDER BY created_at DESC LIMIT 1;
```

**Ожидаемый результат:** Запись с action='user.delete', old_values содержит данные пользователя  
**Статус:** ⬜ Не протестировано

### Тест 8.3: Проверка записи изменения цены

```bash
# 1. Изменить цену товара
# 2. Проверить таблицу admin_audit_logs
SELECT old_values, new_values FROM admin_audit_logs 
WHERE action = 'product.update' 
ORDER BY created_at DESC LIMIT 1;
```

**Ожидаемый результат:** old_values и new_values содержат разные цены  
**Статус:** ⬜ Не протестировано

### Тест 8.4: Проверка IP адреса в audit log

```bash
SELECT ip_address, user_agent FROM admin_audit_logs 
WHERE user_id = ADMIN_ID 
ORDER BY created_at DESC LIMIT 5;
```

**Ожидаемый результат:** IP адреса соответствуют реальным запросам  
**Статус:** ⬜ Не протестировано

### Тест 8.5: Проверка защиты audit log от подделки

```bash
# Попытка удалить запись из audit log
DELETE FROM admin_audit_logs WHERE id = 1;
```

**Ожидаемый результат:** Запись удалена (но это должно логироваться в security_logs)  
**Статус:** ⬜ Не протестировано

---

## 9. ИНСТРУКЦИЯ ПО ЗАПУСКУ ТЕСТОВ

### 9.1 Подготовка окружения

```bash
# 1. Применить миграции
php artisan migrate

# 2. Создать тестового администратора
php artisan tinker
>>> User::create([
    'username' => 'test_admin',
    'email' => 'admin@test.com',
    'password' => Hash::make('password123'),
    'role_id' => 2, // admin
    'is_active' => true
]);

# 3. Очистить кэш
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 9.2 Запуск автоматических тестов (PHPUnit)

```bash
# Создать тесты
php artisan make:test AdminSecurityTest
php artisan make:test AdminAuditLogTest
php artisan make:test AdminRateLimitTest

# Запустить тесты
php artisan test --filter AdminSecurityTest
```

### 9.3 Ручное тестирование

1. Открыть браузер в режиме инкогнито
2. Войти как admin
3. Выполнить действия из чек-листа
4. Проверить `admin_audit_logs` в базе данных
5. Проверить `security_logs` в базе данных

### 9.4 Проверка логов

```bash
# Просмотр security логов
tail -f storage/logs/security.log

# Просмотр Laravel логов
tail -f storage/logs/laravel.log
```

---

## 📊 СТАТУС ТЕСТОВ

| Категория | Всего тестов | Пройдено | Провалено | Не протестировано |
|-----------|-------------|----------|-----------|-------------------|
| Обход авторизации | 5 | 0 | 0 | 5 |
| Эскалация привилегий | 4 | 0 | 0 | 4 |
| SQL-инъекции | 4 | 0 | 0 | 4 |
| XSS уязвимости | 3 | 0 | 0 | 3 |
| CSRF защита | 3 | 0 | 0 | 3 |
| Rate Limiting | 3 | 0 | 0 | 3 |
| Audit Log | 5 | 0 | 0 | 5 |
| **ИТОГО** | **27** | **0** | **0** | **27** |

---

## 🔧 КОНФИГУРАЦИЯ БЕЗОПАСНОСТИ

### Переменные окружения (.env)

```env
# IP whitelist для администраторов (опционально)
ADMIN_IP_WHITELIST=127.0.0.1,192.168.1.100

# Время жизни сессии (минуты)
SESSION_LIFETIME=120

# 2FA обязательно для админа
ADMIN_2FA_REQUIRED=true
```

### Файлы для проверки

- [x] `app/Http/Middleware/EnsureAdminRole.php`
- [x] `app/Services/AdminSecurityService.php`
- [x] `app/Services/SecurityLogger.php`
- [x] `app/Models/AdminAuditLog.php`
- [x] `app/Http/Controllers/AdminController.php`
- [x] `routes/web.php`
- [x] `bootstrap/app.php`
- [x] `database/migrations/2026_03_29_000003_create_admin_audit_logs_table.php`
- [x] `resources/views/admin/audit-log.blade.php`

---

## ✅ ЧЕК-ЛИСТ ВНЕДРЕНИЯ

- [x] Middleware `EnsureAdminRole.php` создан
- [x] Алиас `ensure.admin` зарегистрирован в `bootstrap/app.php`
- [x] Маршруты `/admin/*` защищены 5 middleware
- [x] SecurityLogger обновлён методами для админа
- [x] Миграция `admin_audit_logs` создана
- [x] Модель `AdminAuditLog` создана
- [x] Сервис `AdminSecurityService` создан
- [x] `AdminController` обновлён с логированием
- [x] View `audit-log.blade.php` создан
- [x] Маршрут `admin.audit-log` добавлен
- [x] Чек-лист безопасности создан

---

**Дата последней проверки:** ________  
**Проверил:** ________  
**Статус:** ⬜ Требуется проверка / ✅ Пройдено
