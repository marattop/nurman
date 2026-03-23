<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use App\Models\QrCode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Находим все купоны без QR-кодов и создаём для них QR-коды
        $couponsWithoutQr = Coupon::whereDoesntHave('qrCode')->get();
        
        foreach ($couponsWithoutQr as $coupon) {
            $qrData = json_encode(['coupon_id' => $coupon->id, 'code' => $coupon->code]);
            
            QrCode::create([
                'variant_id' => null,
                'coupon_id' => $coupon->id,
                'qr_data' => $qrData,
                'is_printed' => false,
            ]);
        }
        
        echo "Сгенерировано QR-кодов: " . $couponsWithoutQr->count() . "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем QR-коды созданные для купонов
        QrCode::whereNotNull('coupon_id')
            ->whereDoesntHave('variant')
            ->delete();
    }
};
