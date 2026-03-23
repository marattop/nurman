<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Models\CustomerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Роли
        $roles = [
            ['id' => 1, 'name' => 'owner', 'display_name' => 'Владелец'],
            ['id' => 2, 'name' => 'admin', 'display_name' => 'Администратор'],
            ['id' => 3, 'name' => 'seller', 'display_name' => 'Продавец'],
            ['id' => 4, 'name' => 'warehouse', 'display_name' => 'Кладовщик'],
            ['id' => 5, 'name' => 'client', 'display_name' => 'Клиент'],
        ];
        foreach ($roles as $roleData) Role::firstOrCreate(['id' => $roleData['id']], $roleData);

        // Пользователи
        User::firstOrCreate(['username' => 'admin'], ['email' => 'admin@nurman.kg', 'phone' => '+996500000000', 'password' => Hash::make('admin123'), 'full_name' => 'Владелец', 'role_id' => 1]);
        User::firstOrCreate(['username' => 'seller1'], ['email' => 'seller@nurman.kg', 'phone' => '+996550000001', 'password' => Hash::make('seller123'), 'full_name' => 'Продавец 1', 'role_id' => 3]);
        User::firstOrCreate(['username' => 'warehouse1'], ['email' => 'warehouse@nurman.kg', 'phone' => '+996550000002', 'password' => Hash::make('warehouse123'), 'full_name' => 'Кладовщик 1', 'role_id' => 4]);
        $client = User::firstOrCreate(['username' => 'client1'], ['email' => 'client@example.com', 'phone' => '+996700000000', 'password' => Hash::make('client123'), 'full_name' => 'Клиент', 'role_id' => 5]);
        CustomerProfile::firstOrCreate(['user_id' => $client->id], ['bonus_points' => 100]);

        // Категории
        foreach (['Костюмы', 'Рубашки', 'Брюки', 'Пиджаки', 'Галстуки', 'Обувь', 'Аксессуары'] as $i => $name) {
            Category::firstOrCreate(['slug' => strtolower(transliterate($name))], ['name' => $name, 'sort_order' => $i]);
        }

        // Магазин и склад
        Shop::firstOrCreate(['code' => 'KARAKOL_MAIN'], ['name' => 'NURMAN - Каракол', 'address' => 'г. Каракол, ул. Ленина, 100', 'phone' => '+996392250000', 'latitude' => 42.4913, 'longitude' => 78.4067]);
        Warehouse::firstOrCreate(['code' => 'MAIN_WAREHOUSE'], ['name' => 'Основной склад', 'address' => 'г. Каракол, ул. Складская, 1']);
    }
}

// Helper function if transliterate doesn't exist
if (!function_exists('transliterate')) {
    function transliterate($string) {
        $converter = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya'];
        return strtr(mb_strtolower($string), $converter);
    }
}
