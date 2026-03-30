<?php $__env->startSection('page-title', 'Клиенты'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-icon"><i class="bi bi-people"></i></div>
    <div>
        <h1 class="page-title">Клиенты</h1>
        <p class="page-subtitle">База клиентов</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #10b981;"></i> Список клиентов</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Клиент</th>
                    <th>Телефон</th>
                    <th>Купон</th>
                    <th>Статус</th>
                    <th style="text-align: center;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $coupon = $customer->myCoupons->first();
                    $hasCoupon = $coupon && $coupon->is_active;
                ?>
                <tr>
                    <td>
                        <div style="font-weight: 500;"><?php echo e($customer->full_name ?? '—'); ?></div>
                    </td>
                    <td style="color: #f1f5f9;"><?php echo e($customer->phone ?? '—'); ?></td>
                    <td>
                        <?php if($hasCoupon): ?>
                            <span class="badge badge-success">
                                <i class="bi bi-tag"></i> <?php echo e($coupon->discount_type === 'percent' ? $coupon->discount_value . '%' : 'Опт'); ?>

                            </span>
                        <?php else: ?>
                            <span class="badge" style="background: rgba(239,68,68,0.15); color: #ef4444;">
                                <i class="bi bi-x-circle"></i> Нет купона
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($customer->is_active): ?>
                            <span class="badge badge-success">
                                <i class="bi bi-check-circle"></i> Активен
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">
                                <i class="bi bi-x-circle"></i> Неактивен
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <button class="btn btn-outline" onclick="viewCustomerHistory(<?php echo e($customer->id); ?>, '<?php echo e($customer->full_name); ?>')" title="История покупок">
                                <i class="bi bi-receipt"></i> Чек
                            </button>
                            <?php if(!$hasCoupon): ?>
                            <button class="btn btn-primary" onclick="createCouponForCustomer(<?php echo e($customer->id); ?>, '<?php echo e($customer->full_name); ?>', '<?php echo e($customer->phone); ?>')" title="Создать скидочную карту">
                                <i class="bi bi-plus-lg"></i> Купон
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Клиенты не найдены</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($customers->hasPages()): ?>
    <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <div style="color: #94a3b8; font-size: 13px;">Показано <?php echo e($customers->firstItem() ?? 0); ?> - <?php echo e($customers->lastItem() ?? 0); ?> из <?php echo e($customers->total()); ?></div>
        <div style="display: flex; gap: 8px;">
            <?php if($customers->onFirstPage()): ?>
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">← Назад</span>
            <?php else: ?>
                <a href="<?php echo e($customers->previousPageUrl()); ?>" style="padding: 8px 14px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; text-decoration: none; font-size: 14px;">← Назад</a>
            <?php endif; ?>
            <?php if($customers->hasMorePages()): ?>
                <a href="<?php echo e($customers->nextPageUrl()); ?>" style="padding: 8px 14px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; text-decoration: none; font-size: 14px;">Вперёд →</a>
            <?php else: ?>
                <span style="padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.05); color: #64748b; text-decoration: none; font-size: 14px;">Вперёд →</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Модальное окно истории покупок -->
<div id="historyModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeHistoryModal()"></div>
    <div class="modal-content" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3><i class="bi bi-receipt"></i> История покупок</h3>
            <button class="btn-close" onclick="closeHistoryModal()">&times;</button>
        </div>
        <p id="historyCustomerName" style="font-weight: 600; margin-bottom: 16px; color: #10b981;"></p>
        <div id="historyContent" style="max-height: 400px; overflow-y: auto;">
            <div style="text-align: center; padding: 2rem; color: #94a3b8;">Загрузка...</div>
        </div>
    </div>
</div>

<!-- Модальное окно создания купона -->
<div id="couponModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeCouponModal()"></div>
    <div class="modal-content" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3><i class="bi bi-tag"></i> Создание скидочной карты</h3>
            <button class="btn-close" onclick="closeCouponModal()">&times;</button>
        </div>
        <form id="couponForm" onsubmit="createCoupon(event)">
            <input type="hidden" id="couponCustomerId">
            <p id="couponCustomerInfo" style="margin-bottom: 20px; color: #94a3b8;"></p>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Тип скидки</label>
                <select name="discount_type" id="couponType" onchange="toggleDiscountValue()" style="width: 100%; padding: 12px; border-radius: 10px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #f1f5f9;">
                    <option value="percent">Процентная</option>
                    <option value="wholesale">Оптовая цена</option>
                </select>
            </div>
            
            <div id="percentValueBlock" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Процент скидки</label>
                <input type="number" name="discount_value" id="couponPercent" value="10" min="1" max="100" style="width: 100%; padding: 12px; border-radius: 10px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #f1f5f9;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Срок действия</label>
                <select name="validity_period" id="couponValidity" style="width: 100%; padding: 12px; border-radius: 10px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #f1f5f9;">
                    <option value="12">12 месяцев</option>
                    <option value="6">6 месяцев</option>
                    <option value="3">3 месяца</option>
                    <option value="999">Бессрочно</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeCouponModal()" style="flex: 1;">Отмена</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="bi bi-check-lg"></i> Создать
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.modal { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; }
.modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.7); }
.modal-content { position: relative; background: #1e293b; border-radius: 16px; padding: 24px; width: 90%; max-width: 500px; border: 1px solid rgba(255,255,255,0.1); }
.btn-close { background: transparent; border: none; color: #94a3b8; font-size: 28px; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
.btn-close:hover { color: #f1f5f9; }
</style>

<script>
function viewCustomerHistory(customerId, customerName) {
    document.getElementById('historyCustomerName').textContent = customerName;
    document.getElementById('historyModal').style.display = 'flex';
    
    fetch(`/seller/customers/${customerId}/history`)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('historyContent');
            if (data.sales && data.sales.length > 0) {
                let html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
                data.sales.forEach(sale => {
                    const date = new Date(sale.created_at).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' });
                    html += `
                        <div style="background: #0f172a; border-radius: 12px; padding: 16px; border: 1px solid rgba(255,255,255,0.05);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div>
                                    <div style="color: #f1f5f9; font-size: 14px; font-weight: 500;">${sale.transaction_number}</div>
                                    <div style="color: #94a3b8; font-size: 12px;">${date}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="color: #10b981; font-weight: 600; font-size: 16px;">${sale.total_amount.toLocaleString()} с</div>
                                    <button onclick="printReceipt(${sale.id})" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; margin-top: 4px;">
                                        <i class="bi bi-printer"></i> Печать
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #94a3b8;"><i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i><br>Покупок нет</div>';
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('historyContent').innerHTML = '<div style="text-align: center; padding: 2rem; color: #ef4444;">Ошибка загрузки</div>';
        });
}

function printReceipt(transactionId) {
    window.open('/seller/pos/receipt/' + transactionId, '_blank', 'width=400,height=700');
}

function closeHistoryModal() {
    document.getElementById('historyModal').style.display = 'none';
}

function createCouponForCustomer(customerId, customerName, phone) {
    document.getElementById('couponCustomerId').value = customerId;
    document.getElementById('couponCustomerInfo').textContent = `${customerName} (${phone})`;
    document.getElementById('couponModal').style.display = 'flex';
    toggleDiscountValue();
}

function closeCouponModal() {
    document.getElementById('couponModal').style.display = 'none';
    document.getElementById('couponForm').reset();
}

function toggleDiscountValue() {
    const type = document.getElementById('couponType').value;
    const percentBlock = document.getElementById('percentValueBlock');
    percentBlock.style.display = type === 'percent' ? 'block' : 'none';
}

function createCoupon(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const data = {
        customer_id: document.getElementById('couponCustomerId').value,
        discount_type: formData.get('discount_type'),
        discount_value: formData.get('discount_value') || 0,
        validity_period: formData.get('validity_period'),
    };
    
    fetch('<?php echo e(route("seller.customers.create-coupon")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Купон успешно создан!');
            closeCouponModal();
            location.reload();
        } else {
            alert('Ошибка: ' + (result.message || 'Не удалось создать купон'));
        }
    })
    .catch(err => {
        alert('Ошибка сети: ' + err);
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller::layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/seller/customers/index.blade.php ENDPATH**/ ?>