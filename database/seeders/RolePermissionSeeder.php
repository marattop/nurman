<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder для заполнения системы ролями и разрешениями (RBAC).
 * 
 * Создает:
 * - 4 базовые роли: Owner, Admin, Seller, Warehouse
 * - Все необходимые разрешения по группам
 * - Привязку разрешений к ролям
 * - Первого пользователя Owner (admin/admin)
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Запуск сеедера.
     */
    public function run(): void
    {
        // ====================================================================
        // 1. Создаём разрешения по группам
        // ====================================================================

        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                    'description' => $permission['description'] ?? null,
                ]
            );
        }

        // ====================================================================
        // 2. Создаём роли
        // ====================================================================

        $roles = $this->getRoles();

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'level' => $roleData['level'],
                    'is_active' => true,
                ]
            );
        }

        // ====================================================================
        // 3. Привязываем разрешения к ролям
        // ====================================================================

        $this->syncRolePermissions();

        // ====================================================================
        // 4. Создаём пользователей по умолчанию (Owner и Admin)
        // ====================================================================

        $this->createDefaultUsers();

        $this->command->info('✅ Роли и разрешения успешно созданы!');
    }

    /**
     * Получить все разрешения по группам.
     */
    private function getPermissions(): array
    {
        return [
            // ==================== PRODUCTS ====================
            ['name' => 'products.view', 'display_name' => 'Просмотр товаров', 'group' => 'products', 'description' => 'Просмотр каталога товаров'],
            ['name' => 'products.create', 'display_name' => 'Создание товаров', 'group' => 'products', 'description' => 'Добавление новых товаров'],
            ['name' => 'products.edit', 'display_name' => 'Редактирование товаров', 'group' => 'products', 'description' => 'Изменение данных товаров'],
            ['name' => 'products.delete', 'display_name' => 'Удаление товаров', 'group' => 'products', 'description' => 'Удаление товаров из каталога'],
            ['name' => 'products.prices', 'display_name' => 'Управление ценами', 'group' => 'products', 'description' => 'Изменение розничных цен'],
            ['name' => 'products.cost', 'display_name' => 'Просмотр себестоимости', 'group' => 'products', 'description' => 'Просмотр закупочных цен'],
            ['name' => 'products.discounts', 'display_name' => 'Управление скидками', 'group' => 'products', 'description' => 'Создание акций и скидок'],

            // ==================== SALES ====================
            ['name' => 'sales.view', 'display_name' => 'Просмотр продаж', 'group' => 'sales', 'description' => 'Просмотр истории продаж'],
            ['name' => 'sales.create', 'display_name' => 'Создание продажи', 'group' => 'sales', 'description' => 'Пробитие чека, оформление продажи'],
            ['name' => 'sales.return', 'display_name' => 'Оформление возврата', 'group' => 'sales', 'description' => 'Создание возврата товара'],
            ['name' => 'sales.return.approve', 'display_name' => 'Подтверждение возврата', 'group' => 'sales', 'description' => 'Подтверждение сложных возвратов'],
            ['name' => 'sales.refund', 'display_name' => 'Возврат денег', 'group' => 'sales', 'description' => 'Оформление возврата денег клиенту'],

            // ==================== FINANCE ====================
            ['name' => 'finance.view', 'display_name' => 'Просмотр финансов', 'group' => 'finance', 'description' => 'Просмотр финансовой информации'],
            ['name' => 'finance.revenue', 'display_name' => 'Просмотр выручки', 'group' => 'finance', 'description' => 'Просмотр общей выручки'],
            ['name' => 'finance.cost', 'display_name' => 'Просмотр себестоимости', 'group' => 'finance', 'description' => 'Просмотр закупочной стоимости'],
            ['name' => 'finance.profit', 'display_name' => 'Просмотр прибыли', 'group' => 'finance', 'description' => 'Просмотр чистой прибыли'],
            ['name' => 'finance.margin', 'display_name' => 'Просмотр маржинальности', 'group' => 'finance', 'description' => 'Просмотр маржинальности товаров'],
            ['name' => 'finance.expenses', 'display_name' => 'Управление расходами', 'group' => 'finance', 'description' => 'Добавление и просмотр расходов'],
            ['name' => 'finance.balance', 'display_name' => 'Просмотр баланса', 'group' => 'finance', 'description' => 'Просмотр общего баланса компании'],

            // ==================== USERS ====================
            ['name' => 'users.view', 'display_name' => 'Просмотр пользователей', 'group' => 'users', 'description' => 'Просмотр списка сотрудников'],
            ['name' => 'users.create', 'display_name' => 'Создание пользователей', 'group' => 'users', 'description' => 'Добавление новых сотрудников'],
            ['name' => 'users.edit', 'display_name' => 'Редактирование пользователей', 'group' => 'users', 'description' => 'Изменение данных сотрудников'],
            ['name' => 'users.delete', 'display_name' => 'Удаление пользователей', 'group' => 'users', 'description' => 'Удаление сотрудников'],
            ['name' => 'users.roles', 'display_name' => 'Управление ролями', 'group' => 'users', 'description' => 'Назначение и изменение ролей'],
            ['name' => 'users.salary', 'display_name' => 'Управление зарплатами', 'group' => 'users', 'description' => 'Назначение зарплат и процентов'],

            // ==================== SETTINGS ====================
            ['name' => 'settings.view', 'display_name' => 'Просмотр настроек', 'group' => 'settings', 'description' => 'Просмотр системных настроек'],
            ['name' => 'settings.edit', 'display_name' => 'Редактирование настроек', 'group' => 'settings', 'description' => 'Изменение системных настроек'],
            ['name' => 'settings.shops', 'display_name' => 'Управление магазинами', 'group' => 'settings', 'description' => 'Добавление и редактирование магазинов'],
            ['name' => 'settings.warehouses', 'display_name' => 'Управление складами', 'group' => 'settings', 'description' => 'Добавление и редактирование складов'],

            // ==================== WAREHOUSE ====================
            ['name' => 'warehouse.view', 'display_name' => 'Просмотр склада', 'group' => 'warehouse', 'description' => 'Просмотр остатков на складе'],
            ['name' => 'warehouse.receive', 'display_name' => 'Приёмка товара', 'group' => 'warehouse', 'description' => 'Оформление поступления товара'],
            ['name' => 'warehouse.writeoff', 'display_name' => 'Списание товара', 'group' => 'warehouse', 'description' => 'Списание товара (брак, потеря)'],
            ['name' => 'warehouse.inventory', 'display_name' => 'Инвентаризация', 'group' => 'warehouse', 'description' => 'Проведение инвентаризации'],
            ['name' => 'warehouse.qr', 'display_name' => 'Генерация QR-кодов', 'group' => 'warehouse', 'description' => 'Создание и печать QR-кодов'],
            ['name' => 'warehouse.move', 'display_name' => 'Перемещение товара', 'group' => 'warehouse', 'description' => 'Перемещение между складами/магазинами'],

            // ==================== REPORTS ====================
            ['name' => 'reports.view', 'display_name' => 'Просмотр отчётов', 'group' => 'reports', 'description' => 'Просмотр стандартных отчётов'],
            ['name' => 'reports.sales', 'display_name' => 'Отчёты по продажам', 'group' => 'reports', 'description' => 'Аналитика продаж'],
            ['name' => 'reports.products', 'display_name' => 'Отчёты по товарам', 'group' => 'reports', 'description' => 'Аналитика товаров'],
            ['name' => 'reports.export', 'display_name' => 'Экспорт отчётов', 'group' => 'reports', 'description' => 'Выгрузка отчётов в файлы'],

            // ==================== CUSTOMERS ====================
            ['name' => 'customers.view', 'display_name' => 'Просмотр клиентов', 'group' => 'customers', 'description' => 'Просмотр базы клиентов'],
            ['name' => 'customers.create', 'display_name' => 'Создание клиентов', 'group' => 'customers', 'description' => 'Добавление новых клиентов'],
            ['name' => 'customers.edit', 'display_name' => 'Редактирование клиентов', 'group' => 'customers', 'description' => 'Изменение данных клиентов'],
            ['name' => 'customers.bonus', 'display_name' => 'Управление бонусами', 'group' => 'customers', 'description' => 'Начисление и списание бонусов'],
            ['name' => 'customers.loyalty', 'display_name' => 'Программа лояльности', 'group' => 'customers', 'description' => 'Управление программой лояльности'],

            // ==================== LOGS ====================
            ['name' => 'logs.view', 'display_name' => 'Просмотр логов', 'group' => 'logs', 'description' => 'Просмотр журнала действий'],
            ['name' => 'logs.export', 'display_name' => 'Экспорт логов', 'group' => 'logs', 'description' => 'Выгрузка журнала действий'],
        ];
    }

    /**
     * Получить данные о ролях.
     */
    private function getRoles(): array
    {
        return [
            [
                'name' => 'owner',
                'display_name' => 'Владелец',
                'description' => 'Полный доступ ко всем функциям системы. Видит финансы, управляет персоналом.',
                'level' => Role::LEVEL_OWNER,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Управление магазином: товары, продажи, клиенты. Не видит себестоимость и чистую прибыль.',
                'level' => Role::LEVEL_ADMIN,
            ],
            [
                'name' => 'seller',
                'display_name' => 'Продавец',
                'description' => 'POS-терминал: оформление продаж, работа с клиентами. Ограниченный доступ.',
                'level' => Role::LEVEL_SELLER,
            ],
            [
                'name' => 'warehouse',
                'display_name' => 'Кладовщик',
                'description' => 'Управление складом: приёмка, инвентаризация, QR-коды. Без доступа к финансам.',
                'level' => Role::LEVEL_WAREHOUSE,
            ],
            [
                'name' => 'client',
                'display_name' => 'Клиент',
                'description' => 'Профиль покупателя: история покупок, бонусы, персональные скидки.',
                'level' => Role::LEVEL_CLIENT,
            ],
        ];
    }

    /**
     * Синхронизировать разрешения с ролями.
     */
    private function syncRolePermissions(): void
    {
        $ownerRole = Role::getOwnerRole();
        $adminRole = Role::getAdminRole();
        $sellerRole = Role::getSellerRole();
        $warehouseRole = Role::getWarehouseRole();

        if ($ownerRole) {
            // Owner: ВСЕ разрешения
            $ownerRole->syncPermissions(Permission::all()->pluck('name')->toArray());
        }

        if ($adminRole) {
            // Admin: всё кроме финансов (себестоимость, прибыль)
            $adminPermissions = [
                // Products
                'products.view', 'products.create', 'products.edit', 'products.delete',
                'products.prices', 'products.discounts',
                // Sales
                'sales.view', 'sales.create', 'sales.return', 'sales.return.approve', 'sales.refund',
                // Finance (только выручка, без себестоимости и прибыли)
                'finance.view', 'finance.revenue', 'finance.expenses',
                // Users
                'users.view', 'users.create', 'users.edit', 'users.delete', 'users.roles',
                // Settings
                'settings.view', 'settings.edit', 'settings.shops', 'settings.warehouses',
                // Reports
                'reports.view', 'reports.sales', 'reports.products', 'reports.export',
                // Customers
                'customers.view', 'customers.create', 'customers.edit', 'customers.bonus', 'customers.loyalty',
                // Logs
                'logs.view',
            ];
            $adminRole->syncPermissions($adminPermissions);
        }

        if ($sellerRole) {
            // Seller: только продажи и клиенты
            $sellerPermissions = [
                // Products (только просмотр)
                'products.view',
                // Sales
                'sales.view', 'sales.create', 'sales.return',
                // Finance (только личная выручка - реализуется через контроллер)
                'finance.view', 'finance.revenue',
                // Customers
                'customers.view', 'customers.create', 'customers.bonus',
                // Reports (только свои продажи)
                'reports.view', 'reports.sales',
            ];
            $sellerRole->syncPermissions($sellerPermissions);
        }

        if ($warehouseRole) {
            // Warehouse: только склад
            $warehousePermissions = [
                // Products (просмотр и создание)
                'products.view', 'products.create',
                // Warehouse
                'warehouse.view', 'warehouse.receive', 'warehouse.writeoff',
                'warehouse.inventory', 'warehouse.qr', 'warehouse.move',
                // Reports (только складские отчёты)
                'reports.view', 'reports.products',
            ];
            $warehouseRole->syncPermissions($warehousePermissions);
        }
    }

    /**
     * Создать пользователей по умолчанию (Owner и Admin).
     */
    private function createDefaultUsers(): void
    {
        $ownerRole = Role::getOwnerRole();
        $adminRole = Role::getAdminRole();

        if (!$ownerRole || !$adminRole) {
            $this->command->error('❌ Роли не найдены!');
            return;
        }

        // Создаём Owner (Владелец)
        $owner = User::where('username', 'owner')->first();
        if (!$owner) {
            User::create([
                'username' => 'owner',
                'email' => 'owner@nurman.kg',
                'phone' => null,
                'password' => Hash::make('owner123'),
                'full_name' => 'Владелец NURMAN',
                'role_id' => $ownerRole->id,
                'is_active' => true,
                'avatar' => null,
            ]);
            $this->command->info('✅ Создан ВЛАДЕЛЕЦ: owner / owner123');
        } else {
            $this->command->info('ℹ️ Владелец уже существует');
        }

        // Создаём Admin (Администратор)
        $admin = User::where('username', 'admin')->first();
        if (!$admin) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@nurman.kg',
                'phone' => '+996555000000',
                'password' => Hash::make('admin123'),
                'full_name' => 'Администратор NURMAN',
                'role_id' => $adminRole->id,
                'is_active' => true,
                'avatar' => null,
            ]);
            $this->command->info('✅ Создан АДМИНИСТРАТОР: admin / admin123');
        } else {
            $this->command->info('ℹ️ Администратор уже существует');
        }
    }
}
