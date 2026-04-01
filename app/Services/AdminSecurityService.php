<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminSecurityService
{
    /**
     * Проверка IP адреса на принадлежность к Кыргызстану и отсутствие VPN/Proxy.
     * 
     * Настройка в .env:
     * ALLOWED_COUNTRIES=KG,RU,KZ - разрешённые страны
     * BLOCK_VPN=true - блокировать VPN/Proxy
     */
    public function validateRequest($ip): array
    {
        // Пропускаем локальные адреса для разработки
        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.')) {
            return ['valid' => true, 'reason' => 'local'];
        }

        $allowedCountries = explode(',', env('ALLOWED_COUNTRIES', 'KG'));
        $blockVpn = env('BLOCK_VPN', true);

        try {
            // Используем ip-api.com для проверки геолокации и прокси
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,message,countryCode,proxy,hosting");

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success') {
                    // Проверка на прокси или хостинг (признаки VPN)
                    if ($blockVpn && (($data['proxy'] ?? false) || ($data['hosting'] ?? false))) {
                        return [
                            'valid' => false,
                            'reason' => 'vpn_detected'
                        ];
                    }

                    // Проверка страны (если список разрешённых не пуст)
                    if (!empty($allowedCountries) && !in_array($data['countryCode'], $allowedCountries)) {
                        return [
                            'valid' => false,
                            'reason' => 'foreign_ip',
                            'country' => $data['countryCode']
                        ];
                    }

                    return ['valid' => true];
                }
            }
        } catch (\Exception $e) {
            Log::error("Security Service Error: " . $e->getMessage());
        }

        return ['valid' => true];
    }

    /**
     * Проверка секретного ключа (текущий год).
     */
    public function validateSecretKey($key): bool
    {
        $currentYear = date('Y'); // 2026
        return (string)$key === (string)$currentYear;
    }
}
