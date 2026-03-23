<?php $__env->startSection('title', 'Клиенты - Продавец'); ?>

<?php $__env->startSection('content'); ?>
<style>
    :root {
        --pos-header-height: 60px;
    }

    .customers-container {
        padding: 1.5rem;
        background: var(--bg-secondary);
        min-height: calc(100vh - var(--pos-header-height));
    }

    .customers-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .customers-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-primary,
    .btn-secondary {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-primary {
        background: var(--gradient);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: var(--bg-card);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-card-hover);
    }

    .customers-table {
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .customers-table-header {
        padding: 1rem 1.5rem;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
        font-weight: 700;
        color: var(--text-primary);
    }

    .customers-table-row {
        display: grid;
        grid-template-columns: 2fr 2fr 1fr 1fr;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
    }

    .customers-table-row:last-child {
        border-bottom: none;
    }

    .customers-table-cell {
        color: var(--text-primary);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        text-decoration: none;
        transition: 0.2s;
    }

    .pagination a:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .pagination .active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    /* Модальные окна */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--bg-card);
        border-radius: 16px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-card);
        color: var(--text-primary);
        font-size: 1rem;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
    }

    .form-hint {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .discount-type-group {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .discount-type-option {
        flex: 1;
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .discount-type-option:hover {
        border-color: var(--primary);
    }

    .discount-type-option.active {
        border-color: var(--primary);
        background: rgba(16, 185, 129, 0.1);
        color: var(--primary);
    }

    .coupons-list {
        margin-top: 1rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .coupon-item {
        padding: 1rem;
        background: var(--bg-secondary);
        border-radius: 8px;
        margin-bottom: 0.75rem;
        border-left: 4px solid var(--primary);
    }

    .coupon-code {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
    }

    .coupon-details {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
    }

    .btn-block {
        width: 100%;
        margin-top: 1rem;
    }
</style>

<div class="customers-container">
    <div class="customers-header">
        <h1 class="customers-title">Клиенты</h1>
        <div class="header-actions">
            <button class="btn-secondary" onclick="openRegisterModal()">
                Регистрация клиента
            </button>
            <a href="<?php echo e(route('seller.pos.index')); ?>" class="btn-primary">
                Назад в POS
            </a>
        </div>
    </div>

    <div class="customers-table">
        <div class="customers-table-header">
            <div class="customers-table-row" style="border-bottom: none;">
                <div>Клиент</div>
                <div>Телефон</div>
                <div>Тип купона</div>
                <div>Скидка</div>
                <div>Статус</div>
                <div>QR-код</div>
                <div>Действия</div>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="customers-table-row">
                <div class="customers-table-cell"><?php echo e($coupon->customer?->full_name ?? '—'); ?></div>
                <div class="customers-table-cell"><?php echo e($coupon->customer?->phone ?? '—'); ?></div>
                <div class="customers-table-cell">
                    <?php if($coupon->discount_type === 'percent'): ?>
                        <span style="color: var(--primary);"><i class="bi bi-percent"></i> Процент</span>
                    <?php else: ?>
                        <span style="color: var(--accent-orange);"><i class="bi bi-bag"></i> Опт</span>
                    <?php endif; ?>
                </div>
                <div class="customers-table-cell">
                    <?php if($coupon->discount_type === 'wholesale'): ?>
                        <span style="color: var(--accent-orange);">Оптовая цена</span>
                    <?php else: ?>
                        <?php echo e($coupon->discount_value); ?>%
                    <?php endif; ?>
                </div>
                <div class="customers-table-cell">
                    <?php if($coupon->is_active && $coupon->usage_count < $coupon->usage_limit): ?>
                        <span style="color: var(--success);"><i class="bi bi-check-circle"></i> Активен</span>
                    <?php else: ?>
                        <span style="color: var(--accent-red);"><i class="bi bi-x-circle"></i> Неактивен</span>
                    <?php endif; ?>
                </div>
                <div class="customers-table-cell">
                    <?php
                        $qrCode = $coupon->qrCode;
                    ?>
                    <?php if($qrCode): ?>
                        <button class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="showQrCode(<?php echo e($coupon->id); ?>, '<?php echo e($coupon->code); ?>', '<?php echo e($coupon->customer->full_name ?? 'Клиент'); ?>', '<?php echo e($coupon->customer->phone ?? ''); ?>')">
                            <i class="bi bi-qr-code"></i> Есть
                        </button>
                    <?php else: ?>
                        <span style="color: var(--text-secondary);"><i class="bi bi-qr-code-slash"></i> Нет</span>
                    <?php endif; ?>
                </div>
                <div class="customers-table-cell" style="display: flex; gap: 0.5rem;">
                    <button class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="openCouponModal(<?php echo e($coupon->customer_id); ?>, '<?php echo e($coupon->customer?->full_name ?? 'Клиент'); ?>')">
                        <i class="bi bi-plus-lg"></i> Купон
                    </button>
                    <button class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="viewCoupons(<?php echo e($coupon->customer_id); ?>)">
                        <i class="bi bi-eye"></i> Купоны
                    </button>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-ticket"></i>
                </div>
                <p>Купонов пока нет</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($coupons->hasPages()): ?>
        <div class="pagination">
            <?php echo e($coupons->links()); ?>

        </div>
    <?php endif; ?>
</div>

<!-- Модальное окно регистрации -->
<div class="modal-overlay" id="registerModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Регистрация клиента</h3>
            <button class="modal-close" onclick="closeRegisterModal()">&times;</button>
        </div>
        <form id="registerForm" onsubmit="registerCustomer(event)">
            <div class="form-group">
                <label class="form-label">ФИО</label>
                <input type="text" class="form-input" name="full_name" required placeholder="Иванов Иван Иванович">
            </div>
            <div class="form-group">
                <label class="form-label">Телефон (логин)</label>
                <input type="text" class="form-input" name="phone" required placeholder="+996552299244">
                <div class="form-hint">Телефон будет использоваться как логин для входа</div>
            </div>
            <div class="form-group">
                <label class="form-label">Пароль</label>
                <input type="password" class="form-input" name="password" required minlength="6" placeholder="Минимум 6 символов">
            </div>
            <button type="submit" class="btn-primary btn-block">
                Зарегистрировать
            </button>
        </form>
    </div>
</div>

<!-- Модальное окно создания купона -->
<div class="modal-overlay" id="couponModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Создание купона</h3>
            <button class="modal-close" onclick="closeCouponModal()">&times;</button>
        </div>
        <form id="couponForm" onsubmit="createCoupon(event)">
            <input type="hidden" name="customer_id" id="couponCustomerId">
            <div style="margin-bottom: 1rem; padding: 0.75rem; background: var(--bg-secondary); border-radius: 8px;">
                <strong id="couponCustomerName"></strong>
            </div>
            
            <div class="form-group">
                <label class="form-label">Тип скидки</label>
                <div class="discount-type-group">
                    <div class="discount-type-option active" data-type="percent" onclick="selectDiscountType('percent')">
                        Процент (%)
                    </div>
                    <div class="discount-type-option" data-type="fixed" onclick="selectDiscountType('fixed')">
                        Фикс (с)
                    </div>
                    <div class="discount-type-option" data-type="wholesale" onclick="selectDiscountType('wholesale')">
                        Оптовая цена
                    </div>
                </div>
                <input type="hidden" name="discount_type" id="discountType" value="percent">
            </div>

            <div class="form-group" id="discountValueGroup">
                <label class="form-label">Размер скидки</label>
                <input type="number" class="form-input" name="discount_value" id="discountValue" required min="0" step="0.01" placeholder="10">
                <div class="form-hint" id="discountValueHint">Процент скидки от суммы покупки</div>
            </div>

            <div class="form-group">
                <label class="form-label">Мин. сумма покупки (с)</label>
                <input type="number" class="form-input" name="min_purchase_amount" min="0" placeholder="0">
            </div>

            <div class="form-group">
                <label class="form-label">Лимит использований</label>
                <input type="number" class="form-input" name="usage_limit" min="1" value="1" placeholder="1">
            </div>

            <div class="form-group">
                <label class="form-label">Действует до</label>
                <input type="date" class="form-input" name="valid_until">
                <div class="form-hint">По умолчанию 30 дней</div>
            </div>

            <div class="form-group">
                <label class="form-label">Комментарий</label>
                <textarea class="form-input" name="comment" rows="2" placeholder="Примечание к купону"></textarea>
            </div>

            <button type="submit" class="btn-primary btn-block">
                Создать купон
            </button>
        </form>
    </div>
</div>

<!-- Модальное окно просмотра купонов -->
<div class="modal-overlay" id="viewCouponsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Купоны клиента</h3>
            <button class="modal-close" onclick="closeViewCouponsModal()">&times;</button>
        </div>
        <div class="coupons-list" id="couponsList">
            <p style="text-align: center; color: var(--text-secondary);">Загрузка...</p>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра QR-кода -->
<div class="modal-overlay" id="qrCodeModal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="bi bi-qr-code"></i> QR-код купона</h3>
            <button class="modal-close" onclick="closeQrCodeModal()">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center; padding: 2rem;">
            <img id="qrCodeImage" src="" alt="QR-код" style="max-width: 200px; margin-bottom: 1rem;">
            <h4 id="qrCodeCustomerName" style="margin-bottom: 0.5rem;"></h4>
            <p id="qrCodeCustomerPhone" style="color: var(--text-secondary); margin: 0;"></p>
            <p id="qrCodeCouponCode" style="color: var(--primary); font-weight: 600; margin-top: 0.5rem;"></p>
        </div>
        <div class="modal-footer" style="display: flex; gap: 0.5rem; justify-content: center;">
            <button class="btn-primary" onclick="printQrCode()">
                <i class="bi bi-printer"></i> Печать
            </button>
            <button class="btn-secondary" onclick="closeQrCodeModal()">Закрыть</button>
        </div>
    </div>
</div>

<script>
let currentDiscountType = 'percent';

function openRegisterModal() {
    document.getElementById('registerModal').classList.add('active');
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('active');
    document.getElementById('registerForm').reset();
}

function openCouponModal(customerId, customerName) {
    document.getElementById('couponCustomerId').value = customerId;
    document.getElementById('couponCustomerName').textContent = customerName;
    document.getElementById('couponModal').classList.add('active');
}

function closeCouponModal() {
    document.getElementById('couponModal').classList.remove('active');
    document.getElementById('couponForm').reset();
    selectDiscountType('percent');
}

function openViewCouponsModal() {
    document.getElementById('viewCouponsModal').classList.add('active');
}

function closeViewCouponsModal() {
    document.getElementById('viewCouponsModal').classList.remove('active');
}

function selectDiscountType(type) {
    currentDiscountType = type;
    document.getElementById('discountType').value = type;

    document.querySelectorAll('.discount-type-option').forEach(el => {
        el.classList.remove('active');
        if (el.dataset.type === type) {
            el.classList.add('active');
        }
    });

    const valueInput = document.getElementById('discountValue');
    const valueHint = document.getElementById('discountValueHint');
    const valueGroup = document.getElementById('discountValueGroup');

    if (type === 'percent') {
        valueGroup.style.display = 'block';
        valueInput.placeholder = '10';
        valueHint.textContent = 'Процент скидки от суммы покупки';
        valueInput.required = true;
    } else if (type === 'fixed') {
        valueGroup.style.display = 'block';
        valueInput.placeholder = '100';
        valueHint.textContent = 'Фиксированная сумма скидки в сомах';
        valueInput.required = true;
    } else if (type === 'wholesale') {
        valueGroup.style.display = 'none';
        valueInput.value = '0';
        valueInput.required = false;
    }
}

async function registerCustomer(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('<?php echo e(route('seller.customers.register')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });

        const data = await response.json();

        if (data.success) {
            alert('Клиент зарегистрирован: ' + data.customer.full_name);
            closeRegisterModal();
            location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    } catch (e) {
        alert('Ошибка при регистрации: ' + e.message);
    }
}

async function createCoupon(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    if (currentDiscountType === 'wholesale') {
        formData.set('discount_value', '0');
    }

    try {
        const response = await fetch('<?php echo e(route('seller.customers.create-coupon')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });

        const data = await response.json();

        if (data.success) {
            alert('Купон создан: ' + data.coupon.code);
            closeCouponModal();
        } else {
            alert('Ошибка: ' + data.message);
        }
    } catch (e) {
        alert('Ошибка при создании купона: ' + e.message);
    }
}

async function viewCoupons(customerId) {
    try {
        const response = await fetch('/seller/customers/' + customerId + '/coupons');
        const data = await response.json();

        const list = document.getElementById('couponsList');

        if (data.success && data.coupons.length > 0) {
            list.innerHTML = data.coupons.map(coupon => {
                let discountText = '';
                if (coupon.discount_type === 'percent') {
                    discountText = coupon.discount_value + '%';
                } else if (coupon.discount_type === 'wholesale') {
                    discountText = 'Оптовая цена';
                }

                const isActive = coupon.is_active && coupon.usage_count < coupon.usage_limit;
                const statusClass = isActive ? 'success' : 'danger';
                const statusText = isActive ? 'Активен' : 'Неактивен';

                return `
                    <div class="coupon-item">
                        <div class="coupon-code">${coupon.code}</div>
                        <div class="coupon-details">
                            <div>Скидка: ${discountText}</div>
                            <div>Использовано: ${coupon.usage_count} / ${coupon.usage_limit}</div>
                            <div>Статус: <span style="color: var(--${statusClass})">${statusText}</span></div>
                            ${coupon.valid_until ? '<div>Действует до: ' + new Date(coupon.valid_until).toLocaleDateString('ru-RU') + '</div>' : ''}
                            ${coupon.comment ? '<div>Комментарий: ' + coupon.comment + '</div>' : ''}
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            list.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">У клиента нет купонов</p>';
        }

        openViewCouponsModal();
    } catch (e) {
        alert('Ошибка при загрузке купонов: ' + e.message);
    }
}

// Показ QR-кода
function showQrCode(couponId, couponCode, customerName, customerPhone) {
    const qrData = JSON.stringify({
        type: 'coupon',
        coupon_id: couponId,
        code: couponCode
    });
    
    const qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrData);
    
    document.getElementById('qrCodeImage').src = qrCodeUrl;
    document.getElementById('qrCodeCustomerName').textContent = customerName;
    document.getElementById('qrCodeCustomerPhone').textContent = customerPhone || '';
    document.getElementById('qrCodeCouponCode').textContent = couponCode;
    
    document.getElementById('qrCodeModal').classList.add('active');
}

function closeQrCodeModal() {
    document.getElementById('qrCodeModal').classList.remove('active');
}

function printQrCode() {
    const printWindow = window.open('', '', 'width=400,height=600');
    const qrCodeSrc = document.getElementById('qrCodeImage').src;
    const customerName = document.getElementById('qrCodeCustomerName').textContent;
    const couponCode = document.getElementById('qrCodeCouponCode').textContent;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Печать QR-кода</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                img { max-width: 200px; margin: 20px 0; }
                h2 { margin: 10px 0; }
                .code { font-size: 18px; color: var(--accent-primary); font-weight: bold; }
            </style>
        </head>
        <body>
            <h2>${customerName}</h2>
            <img src="${qrCodeSrc}" alt="QR-код">
            <p class="code">${couponCode}</p>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Закрытие модальных окон по клику на overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\nurman\resources\views/seller/customers/index.blade.php ENDPATH**/ ?>