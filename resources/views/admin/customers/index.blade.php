@extends('layouts.admin')
@section('page-title', 'Клиенты')
@section('content')

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="bi bi-x-circle"></i> {{ session('error') }}
</div>
@endif

<div class="page-header">
    <div class="page-icon"><i class="bi bi-heart"></i></div>
    <div>
        <h1 class="page-title">Клиенты</h1>
        <p class="page-subtitle">Управление клиентами и купонными картами</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список клиентов</h3>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="bi bi-plus-lg"></i> Добавить клиента
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Имя</th>
                    <th style="padding: 12px 16px;">Телефон</th>
                    <th style="padding: 12px 16px;">Тип скидки</th>
                    <th style="padding: 12px 16px;">QR код</th>
                    <th style="padding: 12px 16px;">Статус</th>
                    <th style="padding: 12px 16px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                @php
                    $coupon = $customer->myCoupons->first();
                    $hasCoupon = $coupon !== null;
                    $qrCode = $hasCoupon ? $coupon->qrCode : null;
                @endphp
                <tr>
                    <td style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $customer->full_name ?? '—' }}</div>
                    </td>
                    <td style="padding: 12px 16px; color: #94a3b8;">{{ $customer->phone ?? '—' }}</td>
                    <td style="padding: 12px 16px;">
                        @if($hasCoupon)
                            @if($coupon->discount_type === 'percent')
                                <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                    <i class="bi bi-percent"></i> Процентная ({{ $coupon->discount_value }}%)
                                </span>
                            @elseif($coupon->discount_type === 'wholesale')
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="bi bi-tag"></i> Оптовая (закупочная цена)
                                </span>
                            @endif
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td style="padding: 12px 16px;">
                        @if($hasCoupon && $qrCode)
                            <button class="btn btn-outline btn-qr" 
                                data-qr='@json($qrCode->qr_data)' 
                                data-name="{{ $customer->full_name ?? '' }}">
                                <i class="bi bi-qr-code"></i> Показать QR
                            </button>
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td style="padding: 12px 16px;">
                        @if($hasCoupon)
                            @if($coupon->is_active)
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="bi bi-check-circle"></i> Активен
                                </span>
                            @else
                                <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                    <i class="bi bi-x-circle"></i> Неактивен
                                </span>
                            @endif
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td style="padding: 12px 16px;">
                        <div style="display: flex; gap: 8px;">
                            @if($hasCoupon)
                                <button class="btn btn-outline btn-edit" 
                                    data-coupon-id="{{ $coupon->id }}"
                                    data-full-name="{{ $customer->full_name }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-discount-type="{{ $coupon->discount_type }}"
                                    data-discount-value="{{ $coupon->discount_value ?? 0 }}"
                                    data-is-active="{{ $coupon->is_active ? '1' : '0' }}"
                                    data-valid-until="{{ $coupon->valid_until ? $coupon->valid_until->format('Y-m-d') : '' }}"
                                    title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($coupon->is_active)
                                    <button class="btn btn-outline btn-block" data-coupon-id="{{ $coupon->id }}" data-action="block" title="Заблокировать">
                                        <i class="bi bi-lock"></i>
                                    </button>
                                @else
                                    <button class="btn btn-outline btn-block" data-coupon-id="{{ $coupon->id }}" data-action="unblock" title="Разблокировать">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                @endif
                                <button class="btn btn-outline btn-delete-customer"
                                    data-customer-id="{{ $customer->id }}"
                                    data-client-name="{{ $customer->full_name ?? 'Клиент' }}"
                                    title="Удалить клиента" style="color: #ef4444; border-color: #ef4444; margin-left: 8px;">
                                    <i class="bi bi-person-x"></i>
                                </button>
                                <button class="btn btn-outline btn-delete"
                                    data-coupon-id="{{ $coupon->id }}"
                                    data-client-name="{{ $customer->full_name ?? 'Клиент' }}"
                                    title="Удалить купон" style="color: #ef4444; border-color: #ef4444;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button class="btn btn-primary btn-create-coupon" 
                                    data-phone="{{ $customer->phone }}"
                                    data-full-name="{{ $customer->full_name }}"
                                    title="Создать купон">
                                    <i class="bi bi-plus-lg"></i> Купон
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">Клиенты не найдены</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px; border-top: 1px solid rgba(148, 163, 184, 0.1);">
        {{ $customers->links() }}
    </div>
</div>

{{-- Модальное окно создания купона --}}
<div id="createModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeCreateModal()"></div>
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="bi bi-plus-circle"></i> Создать купонную карту</h3>
            <button class="btn-close" onclick="closeCreateModal()">&times;</button>
        </div>
        <form id="createForm" action="{{ route('admin.customers.coupon.create') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Телефон (логин для клиента) <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="phone" id="createPhone" class="form-control" placeholder="(___) ___-___" value="+996" required>
                    <small style="color: #64748b;">Логин клиента = телефон</small>
                </div>
                <div class="form-group">
                    <label>ФИО <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="full_name" id="createFullName" class="form-control" placeholder="Иванов Иван Иванович" required>
                </div>
                <div class="form-group">
                    <label>Тип скидки <span style="color: #ef4444;">*</span></label>
                    <select name="discount_type" id="createDiscountType" class="form-control" required onchange="toggleDiscountValue()">
                        <option value="percent">Процентная скидка</option>
                        <option value="wholesale">Оптовая скидка (закупочная цена)</option>
                    </select>
                </div>
                <div class="form-group" id="discountValueGroup">
                    <label>Размер скидки (%)</label>
                    <input type="number" name="discount_value" id="createDiscountValue" class="form-control" placeholder="10" min="0" max="100" step="0.1">
                </div>
                <div class="form-group">
                    <label>Срок действия</label>
                    <select name="validity_period" id="createValidityPeriod" class="form-control" onchange="toggleCustomValidity()">
                        <option value="12">12 месяцев</option>
                        <option value="24">24 месяца</option>
                        <option value="999">Бессрочно</option>
                        <option value="custom">Выбрать дату...</option>
                    </select>
                </div>
                <div class="form-group" id="customValidityGroup" style="display: none;">
                    <label>Дата окончания</label>
                    <input type="date" name="valid_until" id="createValidUntil" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeCreateModal()">Отмена</button>
                <button type="submit" class="btn btn-primary">Создать</button>
            </div>
        </form>
    </div>
</div>

{{-- Модальное окно редактирования купона --}}
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeEditModal()"></div>
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="bi bi-pencil"></i> Редактировать купонную карту</h3>
            <button class="btn-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="coupon_id" id="editCouponId">
                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="full_name" id="editFullName" class="form-control">
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" id="editPhone" class="form-control" disabled style="background: rgba(148, 163, 184, 0.1);">
                </div>
                <div class="form-group">
                    <label>Тип скидки</label>
                    <select name="discount_type" id="editDiscountType" class="form-control" onchange="toggleEditDiscountValue()">
                        <option value="percent">Процентная скидка</option>
                        <option value="wholesale">Оптовая скидка (закупочная цена)</option>
                    </select>
                </div>
                <div class="form-group" id="editDiscountValueGroup">
                    <label>Размер скидки (%)</label>
                    <input type="number" name="discount_value" id="editDiscountValue" class="form-control" min="0" max="100" step="0.1">
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select name="is_active" id="editIsActive" class="form-control">
                        <option value="1">Активен</option>
                        <option value="0">Неактивен (заблокирован)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Дата окончания</label>
                    <input type="date" name="valid_until" id="editValidUntil" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Отмена</button>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
</div>

{{-- Модальное окно просмотра QR кода --}}
<div id="qrModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeQrModal()"></div>
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div class="modal-header">
            <h3><i class="bi bi-qr-code"></i> QR код клиента</h3>
            <button class="btn-close" onclick="closeQrModal()">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center;">
            <p id="qrClientName" style="font-weight: 600; margin-bottom: 16px;"></p>
            <div id="qrCodeContainer" style="background: white; padding: 24px; border-radius: 12px; display: inline-block;"></div>
            <p style="margin-top: 16px; color: #64748b; font-size: 14px;">Покажите этот QR код клиенту для сканирования</p>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        display: none;
    }
    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
        position: relative;
        background: #1e293b;
        border-radius: 12px;
        margin: 80px auto;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
    }
    @keyframes modalSlideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #f1f5f9;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 24px;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }
    .btn-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
    .btn-close:hover {
        background: rgba(148, 163, 184, 0.1);
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #e2e8f0;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 6px;
        background: rgba(15, 23, 42, 0.5);
        color: #f1f5f9;
        font-size: 14px;
    }
    .form-control:focus {
        outline: none;
        border-color: #38bdf8;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const ROUTES = {
    couponCreate: '{{ route("admin.customers.coupon.create") }}',
    couponUpdate: (id) => `/admin/customers/coupon/${id}/update`,
    couponBlock: (id) => `/admin/customers/coupon/${id}/block`,
    couponUnblock: (id) => `/admin/customers/coupon/${id}/unblock`,
    couponDelete: (id) => `/admin/customers/coupon/${id}/delete`,
    customerDelete: (id) => `/admin/customers/${id}/delete`,
};

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function openCreateModal(phone = '', fullName = '') {
    document.getElementById('createPhone').value = phone;
    document.getElementById('createFullName').value = fullName;
    document.getElementById('createModal').style.display = 'block';
}

function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
    document.getElementById('createForm').reset();
}

function toggleDiscountValue() {
    const type = document.getElementById('createDiscountType').value;
    document.getElementById('discountValueGroup').style.display = type === 'percent' ? 'block' : 'none';
    if (type === 'wholesale') {
        document.getElementById('createDiscountValue').value = 0;
    }
}

function toggleCustomValidity() {
    const period = document.getElementById('createValidityPeriod').value;
    document.getElementById('customValidityGroup').style.display = period === 'custom' ? 'block' : 'none';
}

function openEditModal(couponId, fullName, phone, discountType, discountValue, isActive, validUntil) {
    document.getElementById('editCouponId').value = couponId;
    document.getElementById('editForm').action = ROUTES.couponUpdate(couponId);
    document.getElementById('editFullName').value = fullName || '';
    document.getElementById('editPhone').value = phone || '';
    document.getElementById('editDiscountType').value = discountType;
    document.getElementById('editDiscountValue').value = discountValue || 0;
    document.getElementById('editIsActive').value = isActive ? '1' : '0';
    document.getElementById('editValidUntil').value = validUntil || '';
    document.getElementById('editModal').style.display = 'block';
    toggleEditDiscountValue();
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function toggleEditDiscountValue() {
    const type = document.getElementById('editDiscountType').value;
    document.getElementById('editDiscountValueGroup').style.display = type === 'percent' ? 'block' : 'none';
}

function showQrCode(qrData, clientName) {
    document.getElementById('qrClientName').textContent = clientName || 'Клиент';
    document.getElementById('qrCodeContainer').innerHTML = '';
    
    try {
        const data = qrData;
        
        if (!data || !data.coupon_id || !data.code) {
            throw new Error('Неверный формат данных');
        }
        
        const qrText = data.coupon_id + ':' + data.code;
        
        const container = document.getElementById('qrCodeContainer');
        container.innerHTML = '';
        
        new QRCode(container, {
            text: qrText,
            width: 256,
            height: 256,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } catch (e) {
        document.getElementById('qrCodeContainer').innerHTML = '<p style="color: #ef4444;">Ошибка: ' + e.message + '</p>';
    }
    
    document.getElementById('qrModal').style.display = 'block';
}

function closeQrModal() {
    document.getElementById('qrModal').style.display = 'none';
}

function toggleCoupon(couponId, action) {
    const url = action === 'block' ? ROUTES.couponBlock(couponId) : ROUTES.couponUnblock(couponId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        alert('Ошибка при выполнении операции');
    });
}

function deleteCoupon(couponId, clientName) {
    if (!confirm('Вы уверены, что хотите удалить купонную карту клиента "' + clientName + '"? Это действие нельзя отменить.')) {
        return;
    }

    fetch(ROUTES.couponDelete(couponId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        alert('Ошибка при удалении купона');
    });
}

function deleteCustomer(customerId, clientName) {
    if (!confirm('Вы уверены, что хотите УДАЛИТЬ КЛИЕНТА "' + clientName + '"?\n\nЭто удалит:\n- Купонную карту\n- Профиль клиента\n- Все данные из базы\n\nЭто действие НЕЛЬЗЯ отменить!')) {
        return;
    }

    fetch(ROUTES.customerDelete(customerId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        alert('Ошибка при удалении клиента');
    });
}

// Event listeners после загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    // QR кнопки
    document.querySelectorAll('.btn-qr').forEach(btn => {
        btn.addEventListener('click', function() {
            const qrDataStr = this.dataset.qr;
            const clientName = this.dataset.name;
            try {
                const qrData = JSON.parse(qrDataStr);
                showQrCode(qrData, clientName);
            } catch (e) {
                alert('Ошибка чтения QR-данных');
            }
        });
    });

    // Кнопки редактирования
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            openEditModal(
                this.dataset.couponId,
                this.dataset.fullName,
                this.dataset.phone,
                this.dataset.discountType,
                parseFloat(this.dataset.discountValue) || 0,
                this.dataset.isActive === '1',
                this.dataset.validUntil
            );
        });
    });

    // Кнопки блокировки/разблокировки
    document.querySelectorAll('.btn-block').forEach(btn => {
        btn.addEventListener('click', function() {
            toggleCoupon(this.dataset.couponId, this.dataset.action);
        });
    });

    // Кнопки удаления
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteCoupon(this.dataset.couponId, this.dataset.clientName);
        });
    });

    // Кнопки удаления клиента
    document.querySelectorAll('.btn-delete-customer').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteCustomer(this.dataset.customerId, this.dataset.clientName);
        });
    });

    // Кнопки создания купона
    document.querySelectorAll('.btn-create-coupon').forEach(btn => {
        btn.addEventListener('click', function() {
            openCreateModal(this.dataset.phone, this.dataset.fullName);
        });
    });

    // Автоформатирование телефона
    const phoneInput = document.getElementById('createPhone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (!value.startsWith('996')) {
                value = '996' + value;
            }
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            e.target.value = '+996 (' + value.substring(3, 6) + ') ' + value.substring(6, 9) + '-' + value.substring(9, 12);
        });
    }
});

// Инициализация
toggleDiscountValue();
</script>
@endpush
