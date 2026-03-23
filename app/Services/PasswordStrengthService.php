<?php

namespace App\Services;

/**
 * Сервис проверки силы пароля.
 */
class PasswordStrengthService
{
    /**
     * Минимальная длина пароля.
     */
    const MIN_LENGTH = 8;

    /**
     * Проверка силы пароля.
     * 
     * @return array ['valid' => bool, 'strength' => string, 'score' => int, 'errors' => array]
     */
    public function checkStrength(string $password): array
    {
        $errors = [];
        $score = 0;

        // Проверка длины
        if (strlen($password) < self::MIN_LENGTH) {
            $errors[] = 'Минимальная длина пароля — ' . self::MIN_LENGTH . ' символов';
        } else {
            $score += 20;
        }

        // Проверка заглавных букв
        if (preg_match('/[A-Z]/', $password)) {
            $score += 20;
        } else {
            $errors[] = 'Пароль должен содержать хотя бы одну заглавную букву';
        }

        // Проверка строчных букв
        if (preg_match('/[a-z]/', $password)) {
            $score += 20;
        } else {
            $errors[] = 'Пароль должен содержать хотя бы одну строчную букву';
        }

        // Проверка цифр
        if (preg_match('/[0-9]/', $password)) {
            $score += 20;
        } else {
            $errors[] = 'Пароль должен содержать хотя бы одну цифру';
        }

        // Проверка специальных символов
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $score += 20;
        } else {
            $errors[] = 'Пароль должен содержать хотя бы один специальный символ (!@#$%^&*...)';
        }

        // Определение уровня силы
        $strength = 'weak';
        if ($score >= 80) {
            $strength = 'strong';
        } elseif ($score >= 60) {
            $strength = 'medium';
        } elseif ($score >= 40) {
            $strength = 'fair';
        }

        return [
            'valid' => $score >= 80,
            'strength' => $strength,
            'score' => $score,
            'errors' => $errors,
        ];
    }

    /**
     * Генерация безопасного пароля.
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*(),.?":{}|<>';
        $password = '';
        $charsLength = strlen($chars);

        // Гарантируем наличие каждого типа символов
        $password .= $this->getRandomChar('abcdefghijklmnopqrstuvwxyz');
        $password .= $this->getRandomChar('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $password .= $this->getRandomChar('0123456789');
        $password .= $this->getRandomChar('!@#$%^&*(),.?":{}|<>');

        // Заполняем оставшуюся длину
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[random_int(0, $charsLength - 1)];
        }

        // Перемешиваем
        return str_shuffle($password);
    }

    /**
     * Получение случайного символа из набора.
     */
    private function getRandomChar(string $charset): string
    {
        return $charset[random_int(0, strlen($charset) - 1)];
    }

    /**
     * Валидация пароля для Laravel.
     */
    public function rules(): array
    {
        return [
            'required',
            'min:' . self::MIN_LENGTH,
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[!@#$%^&*(),.?":{}|<>]/',
        ];
    }

    /**
     * Сообщения об ошибках валидации.
     */
    public function messages(): array
    {
        return [
            'password.regex' => 'Пароль должен содержать заглавные и строчные буквы, цифры и специальные символы',
            'password.min' => 'Минимальная длина пароля — ' . self::MIN_LENGTH . ' символов',
        ];
    }
}
