<?php $__env->startSection('page-title', 'Товары'); ?>
<?php $__env->startSection('content'); ?>
<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
        <i class="bi bi-box-seam"></i>
    </div>
    <div>
        <h2 style="font-size: 28px; font-weight: 700; margin: 0;">Товары</h2>
        <p style="font-size: 14px; color: #94a3b8; margin: 4px 0 0 0;">Управление ассортиментом</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h3 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список товаров
        </h3>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <form method="GET" style="display: flex; align-items: center; gap: 8px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 8px 12px;">
                <i class="bi bi-search" style="color: #94a3b8;"></i>
                <input type="text" name="search" placeholder="Поиск..." value="<?php echo e(request('search')); ?>" style="background: transparent; border: none; color: #f1f5f9; outline: none; font-size: 14px;">
            </form>
            <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Добавить
            </a>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Фото</th>
                    <th style="text-align: left;">Название</th>
                    <th>Артикул</th>
                    <th>Цена</th>
                    <th>Остаток</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <?php if($product->photo): ?>
                            <img src="<?php echo e(asset('storage/' . $product->photo)); ?>" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                                <i class="bi bi-box"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 500;"><?php echo e(Str::limit($product->name, 50)); ?></div>
                        <div style="color: #94a3b8; font-size: 13px;"><?php echo e($product->category?->name ?? ''); ?></div>
                    </td>
                    <td style="color: #94a3b8;"><?php echo e($product->article); ?></td>
                    <td style="color: #10b981; font-weight: 600;"><?php echo e(number_format($product->retail_price ?? 0, 0, '.', ' ')); ?> сом</td>
                    <td>
                        <?php
                            $totalStock = $product->variants->sum(fn($v) => $v->stock->sum('quantity') ?? 0);
                        ?>
                        <?php if($totalStock <= 5): ?>
                            <span class="badge badge-danger"><?php echo e($totalStock); ?> шт</span>
                        <?php else: ?>
                            <span class="badge badge-success"><?php echo e($totalStock); ?> шт</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            
                            <?php
                                $hasQr = false;
                                $firstQrCodes = [];
                                foreach($product->variants as $variant) {
                                    if($variant->qrCodes && $variant->qrCodes->count() > 0) {
                                        $hasQr = true;
                                        $firstQrCodes = $variant->qrCodes->toArray();
                                        break;
                                    }
                                }
                            ?>
                            <?php if($hasQr): ?>
                                <button type="button" onclick="showQrModal(<?php echo e($product->id); ?>, '<?php echo e(addslashes($product->name)); ?>', <?php echo json_encode($firstQrCodes, 15, 512) ?>)" style="padding: 8px 12px; border-radius: 8px; background: rgba(16,185,129,0.15); color: #10b981; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: 14px;" title="Показать QR-код">
                                    <i class="bi bi-qr-code"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo e(route('admin.qr.generate.page', $product->id)); ?>" style="padding: 8px 12px; border-radius: 8px; background: rgba(245,158,11,0.15); color: #f59e0b; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 14px;" title="Сгенерировать QR-код">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" style="padding: 8px 12px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('admin.products.delete', $product->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Удалить?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" style="padding: 8px 12px; border-radius: 8px; background: rgba(239,68,68,0.15); color: #ef4444; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">Товары не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($products->hasPages()): ?>
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <div style="color: #94a3b8; font-size: 13px;">Показано <?php echo e($products->firstItem() ?? 0); ?> - <?php echo e($products->lastItem() ?? 0); ?> из <?php echo e($products->total()); ?></div>
        <div style="display: flex; gap: 8px;">
            <?php if($products->onFirstPage()): ?>
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">← Назад</span>
            <?php else: ?>
                <a href="<?php echo e($products->previousPageUrl()); ?>" style="padding: 8px 14px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; font-size: 14px;">← Назад</a>
            <?php endif; ?>
            <?php if($products->hasMorePages()): ?>
                <a href="<?php echo e($products->nextPageUrl()); ?>" style="padding: 8px 14px; border-radius: 8px; background: rgba(56,189,248,0.1); color: #38bdf8; text-decoration: none; font-size: 14px;">Вперёд →</a>
            <?php else: ?>
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">Вперёд →</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>


<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code"></i> QR-коды товара</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h4 id="qr-product-name" style="font-size: 18px; font-weight: 600; color: #f1f5f9; margin-bottom: 8px;"></h4>
                <div id="qr-codes-container" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center; margin-top: 20px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
function showQrModal(productId, productName, qrCodes) {
    document.getElementById('qr-product-name').textContent = productName;
    
    const container = document.getElementById('qr-codes-container');
    container.innerHTML = '';
    
    qrCodes.forEach(qr => {
        const div = document.createElement('div');
        div.style = 'text-align: center;';
        
        // Генерируем QR-код через API если нет пути
        const qrContent = qr.qr_data && qr.qr_data.article ? qr.qr_data.article : '—';
        const qrImageUrl = qr.qr_code_path ? '/storage/' + qr.qr_code_path : 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(qrContent);
        
        div.innerHTML = `
            <img src="${qrImageUrl}" alt="QR" style="width: 180px; height: 180px; object-fit: contain; border: 2px solid rgba(56,189,248,0.3); border-radius: 12px; margin-bottom: 8px;">
            <div style="font-size: 12px; color: #94a3b8; margin-bottom: 8px;">${qrContent}</div>
            <a href="${qrImageUrl}" download class="btn btn-outline" style="font-size: 12px; padding: 6px 12px;">
                <i class="bi bi-download"></i> Скачать
            </a>
        `;
        container.appendChild(div);
    });
    
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/admin/products/index.blade.php ENDPATH**/ ?>