<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use BaconQrCode\Writer;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
        'variant_id',
        'qr_code_path',
        'qr_data',
        'is_printed',
        'printed_at',
        'printed_by',
    ];

    protected $casts = [
        'qr_data' => 'array',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
    ];

    /**
     * Вариант товара
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Пользователь, который печатал
     */
    public function printedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'printed_by');
    }

    /**
     * Получить URL QR-кода
     */
    public function getQrCodeUrlAttribute(): string
    {
        return asset($this->qr_code_path);
    }

    /**
     * Сгенерировать QR-код для варианта товара
     */
    public static function generateForVariant(ProductVariant $variant, string $directory = 'qrcodes'): self
    {
        // Данные для QR-кода
        $qrData = [
            'type' => 'product',
            'variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'article' => $variant->product->article,
            'name' => $variant->product->name,
            'size' => $variant->size,
            'color' => $variant->color,
            'sku' => $variant->sku,
            'price' => $variant->retail_price,
            'supplier_price' => $variant->supplier_price,
        ];

        // Генерируем уникальный filename
        $filename = 'qr_' . $variant->sku . '_' . time() . '.png';
        $filepath = $directory . '/' . $filename;

        // Создаем директорию если не существует
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }

        // Генерируем QR-код (используем SKU для совместимости)
        $qrCodeContent = $variant->sku;

        // Используем GDLibRenderer (bacon/bacon-qr-code v3)
        $renderer = new \BaconQrCode\Renderer\GDLibRenderer(300);
        $writer = new Writer($renderer);
        $qrCodeImage = $writer->writeString($qrCodeContent, 'UTF-8', \BaconQrCode\Common\ErrorCorrectionLevel::M());

        // Сохраняем файл
        file_put_contents(public_path($filepath), $qrCodeImage);

        // Создаем запись в БД
        return self::create([
            'variant_id' => $variant->id,
            'qr_code_path' => $filepath,
            'qr_data' => $qrData,
            'is_printed' => false,
        ]);
    }

    /**
     * Отметить как напечатанный
     */
    public function markAsPrinted(?int $userId = null): void
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
            'printed_by' => $userId,
        ]);
    }

    /**
     * Получить данные для отображения на этикетке
     */
    public function getLabelDataAttribute(): array
    {
        return [
            'product_name' => $this->variant->product->name,
            'article' => $this->variant->product->article,
            'size' => $this->variant->size,
            'color' => $this->variant->color,
            'price' => $this->variant->retail_price,
            'qr_code' => $this->qr_code_url,
        ];
    }
}
