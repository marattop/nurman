# 🔒 Чек-лист безопасности NURMAN

Этот документ содержит инструкции по тестированию безопасности панели владельца (Owner) и всей системы.

---

## 📋 Содержание

1. [Проверка обхода авторизации](#1-проверка-обхода-авторизации)
2. [Проверка SQL-инъекций](#2-проверка-sql-инъекций)
3. [Проверка XSS (Cross-Site Scripting)](#3-проверка-xss-cross-site-scripting)
4. [Проверка CSRF (Cross-Site Request Forgery)](#4-проверка-csrf-cross-site-request-forgery)
5. [Проверка 2FA bypass](#5-проверка-2fa-bypass)
6. [Проверка сессионного фиксинга](#6-проверка-сессионного-фиксинга)
7. [Проверка brute-force защиты](#7-проверка-brute-force-защиты)
8. [Проверка контроля доступа](#8-проверка-контроля-доступа)
9. [Проверка логирования](#9-проверка-логирования)

---

## 1. Проверка обхода авторизации

### Тест 1.1: Прямой доступ к маршрутам владельца без авторизации

```bash
# Очистите куки и попробуйте получить доступ
curl -X GET http://localhost/owner/dashboard

# Ожидаемый результат: Редирект на /login (302)
```

### Тест 1.2: Доступ с неактивной учётной записью

1. Деактивируйте пользователя в БД:
```sql
UPDATE users SET is_active = 0 WHERE username = 'owner';
```

2. Попробуйте войти

**Ожидаемый результат:** Сообщение о деактивированной учётной записи

### Тест 1.3: Доступ с ролью не-owner к панели владельца

1. Войдите как admin/seller/warehouse
2. Попробуйте перейти на `/owner/dashboard`

**Ожидаемый результат:** Ошибка 403 Forbidden

---

## 2. Проверка SQL-инъекций

### Тест 2.1: Инъекция в форме входа

```bash
curl -X POST http://localhost/login \
  -d "username=' OR '1'='1'--&password=anything"
```

**Ожидаемый результат:** Ошибка аутентификации, не вход

### Тест 2.2: Инъекция в поиске пользователей

```bash
curl -X GET "http://localhost/owner/users?search=' UNION SELECT * FROM users--"
```

**Ожидаемый результат:** Корректная обработка как строки, не SQL-код

### Тест 2.3: Инъекция в редактировании товара

```bash
curl -X POST "http://localhost/owner/products/1/update" \
  -d "name=test'; DROP TABLE products;--&article=TEST"
```

**Ожидаемый результат:** Товар создан с буквальным названием, таблица не удалена

---

## 3. Проверка XSS (Cross-Site Scripting)

### Тест 3.1: XSS в имени пользователя

```bash
curl -X POST http://localhost/owner/users/store \
  -d "username=<script>alert('XSS')</script>&email=test@test.com&password=123456&role_id=3"
```

**Ожидаемый результат:** Скрипт не выполняется, имя отображается как текст

### Тест 3.2: XSS в названии товара

1. Откройте форму редактирования товара
2. Введите в название: `<img src=x onerror=alert('XSS')>`
3. Сохраните

**Ожидаемый результат:** Тег отображается как текст, скрипт не выполняется

### Тест 3.3: XSS в параметрах URL

```bash
curl -X GET "http://localhost/owner/users?search=<script>alert(1)</script>"
```

**Ожидаемый результат:** Скрипт не выполняется

---

## 4. Проверка CSRF (Cross-Site Request Forgery)

### Тест 4.1: POST запрос без CSRF токена

```bash
curl -X POST http://localhost/owner/users/store \
  -d "username=testuser&email=test@test.com&password=123456&role_id=3"
```

**Ожидаемый результат:** Ошибка 419 Page Expired или CSRF token mismatch

### Тест 4.2: CSRF с другого домена

Создайте HTML файл на другом домене:
```html
<form action="http://localhost/owner/users/store" method="POST">
  <input type="hidden" name="username" value="hacker">
  <input type="hidden" name="email" value="hacker@test.com">
  <input type="hidden" name="password" value="123456">
  <input type="hidden" name="role_id" value="3">
</form>
<script>document.forms[0].submit();</script>
```

**Ожидаемый результат:** Запрос отклонён из-за отсутствия CSRF токена

---

## 5. Проверка 2FA bypass

### Тест 5.1: Прямой доступ к dashboard без 2FA верификации

1. Включите 2FA для пользователя
2. Войдите с логином/паролем
3. Сразу перейдите на `/owner/dashboard` до ввода 2FA кода

**Ожидаемый результат:** Редирект на `/2fa`

### Тест 5.2: Подбор 2FA кода

```bash
# Попробуйте разные 6-значные коды
for i in {000000..999999}; do
  curl -X POST http://localhost/2fa/verify -d "code=$i"
done
```

**Ожидаемый результат:** После нескольких попыток - блокировка или задержка

### Тест 5.3: Повторное использование 2FA кода

1. Используйте валидный 2FA код
2. Попробуйте использовать тот же код повторно

**Ожидаемый результат:** Код не принимается повторно (если реализована такая защита)

### Тест 5.4: Доступ к API без 2FA

```bash
curl -X GET http://localhost/owner/users \
  -H "Authorization: Bearer <token>"
```

**Ожидаемый результат:** Требуется 2FA верификация

---

## 6. Проверка сессионного фиксинга

### Тест 6.1: Session Fixation атака

1. Получите session ID до входа:
```bash
curl -c cookies.txt http://localhost/login
```

2. Войдите с тем же session ID:
```bash
curl -b cookies.txt -c cookies.txt -X POST http://localhost/login \
  -d "username=owner&password=secret"
```

3. Проверьте изменился ли session ID:
```bash
curl -b cookies.txt http://localhost/owner/dashboard
```

**Ожидаемый результат:** Session ID изменён после входа (session regeneration)

### Тест 6.2: Перехват сессии

1. Войдите в систему
2. Скопируйте session cookie
3. Откройте инкогнито окно и вставьте cookie
4. Попробуйте получить доступ

**Ожидаемый результат:** 
- Session должна быть привязана к IP/User-Agent
- Или должна истечь при бездействии (30 минут)

### Тест 6.3: Session после logout

1. Войдите в систему
2. Выйдите
3. Попробуйте использовать старую session cookie

**Ожидаемый результат:** Сессия недействительна, редирект на login

---

## 7. Проверка brute-force защиты

### Тест 7.1: Блокировка после 5 неудачных попыток

```bash
for i in {1..6}; do
  echo "Попытка $i:"
  curl -X POST http://localhost/login \
    -d "username=owner&password=wrongpassword"
  echo ""
done
```

**Ожидаемый результат:** 
- После 5 попытки - блокировка на 15 минут
- Сообщение о слишком многих попытках

### Тест 7.2: Разные IP адреса

Попробуйте обойти блокировку с разных IP (через прокси):

```bash
curl -X POST http://localhost/login \
  -H "X-Forwarded-For: 192.168.1.100" \
  -d "username=owner&password=wrongpassword"
```

**Ожидаемый результат:** Блокировка по реальному IP, не по заголовку

### Тест 7.3: Сброс счётчика после успешного входа

1. Сделайте 2 неудачные попытки
2. Войдите правильно
3. Сделайте ещё 5 неудачных попыток

**Ожидаемый результат:** Счётчик сброшен после успешного входа

---

## 8. Проверка контроля доступа (Authorization)

### Тест 8.1: Вертикальная эскалация привилегий

1. Войдите как seller (role_id = 3)
2. Попробуйте получить доступ к `/owner/users`

**Ожидаемый результат:** 403 Forbidden

### Тест 8.2: Горизонтальная эскалация привилегий

1. Войдите как seller1
2. Попробуйте получить доступ к данным seller2:
```bash
curl -X GET http://localhost/seller/users/2/edit
```

**Ожидаемый результат:** Доступ только к своим данным

### Тест 8.3: Доступ к чужим данным через ID

```bash
# Войдите как пользователь с ID 5
# Попробуйте получить данные пользователя с ID 1
curl -X GET http://localhost/owner/users/1/edit
```

**Ожидаемый результат:** Для owner - доступ есть, для других - 403

### Тест 8.4: Mass Assignment уязвимость

```bash
curl -X POST http://localhost/owner/users/store \
  -d "username=test&email=test@test.com&password=123456&role_id=1&is_active=1"
```

**Ожидаемый результат:** Нельзя назначить роль owner через эту форму

---

## 9. Проверка логирования

### Тест 9.1: Логирование попыток входа

1. Войдите в систему
2. Проверьте таблицу `security_logs`:

```sql
SELECT * FROM security_logs 
WHERE event = 'login.success' 
ORDER BY created_at DESC LIMIT 5;
```

**Ожидаемый результат:** Запись о входе с IP, user-agent, timestamp

### Тест 9.2: Логирование неудачных попыток

1. Попробуйте войти с неправильным паролем
2. Проверьте логи:

```sql
SELECT * FROM security_logs 
WHERE event = 'login.failed' 
ORDER BY created_at DESC LIMIT 5;
```

**Ожидаемый результат:** Запись о неудачной попытке

### Тест 9.3: Логирование доступа владельца

1. Войдите как owner
2. Посетите несколько страниц
3. Проверьте логи:

```sql
SELECT * FROM security_logs 
WHERE event = 'owner.access' 
ORDER BY created_at DESC LIMIT 10;
```

**Ожидаемый результат:** Записи о каждом действии владельца

### Тест 9.4: Уведомления о новом устройстве

1. Войдите с нового браузера/устройства
2. Проверьте email владельца

**Ожидаемый результат:** Email уведомление о входе с нового устройства

---

## 🛠 Инструменты для тестирования

### Burp Suite
- Перехват и модификация запросов
- Сканирование уязвимостей
- Тестирование CSRF

### OWASP ZAP
- Бесплатный аналог Burp Suite
- Автоматическое сканирование

### SQLMap
```bash
sqlmap -u "http://localhost/owner/users?search=test" --dbs
```

### XSStrike
```bash
python xsstrike.py -u "http://localhost/owner/users?search=test"
```

---

## ✅ Критерии прохождения

| Тест | Статус | Примечание |
|------|--------|------------|
| Обход авторизации | ❌ | Должен быть заблокирован |
| SQL-инъекции | ❌ | Должны быть предотвращены |
| XSS атаки | ❌ | Должны быть нейтрализованы |
| CSRF атаки | ❌ | Должны быть заблокированы |
| 2FA bypass | ❌ | Должен быть невозможен |
| Session fixation | ❌ | Должен быть предотвращён |
| Brute-force | ❌ | Должен быть заблокирован |
| Escalation привилегий | ❌ | Должна быть заблокирована |
| Логирование | ✅ | Должно работать |

---

## 📞 Контакты

При обнаружении уязвимостей сообщите:
- Email: security@nurman.kg
- Внутренний тикет: Security Team

---

**Последнее обновление:** 29 марта 2026
**Версия документа:** 1.0
