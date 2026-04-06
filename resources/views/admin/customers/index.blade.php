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
        <table class="table responsive-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px;">Имя</th>
                    <th style="padding: 12px 16px;">Телефон</th>
                    <th style="padding: 12px 16px;">Покупки</th>
                    <th style="padding: 12px 16px;">Сэкономлено</th>
                    <th style="padding: 12px 16px;">Тип скидки</th>
                    <th style="padding: 12px 16px;">QR код</th>
                    <th style="padding: 12px 16px;">Статус</th>
                    <th style="padding: 12px 16px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                @php
                    $profile = $customer->customerProfile;
                    $coupon = $customer->myCoupons->first();
                    $hasCoupon = $coupon !== null;
                    $qrCode = $hasCoupon ? $coupon->qrCode : null;
                    
                    // Статистика покупок
                    $totalPurchases = $profile ? $profile->total_purchases ?? 0 : 0;
                    $purchaseCount = $profile ? $profile->purchase_count ?? 0 : 0;
                    $loyaltyLevel = $profile ? $profile->loyalty_level : 'Standard';
                    
                    // Сэкономленная сумма (расчетная)
                    $totalSaved = 0;
                    if ($hasCoupon && $coupon->discount_type === 'percent') {
                        $totalSaved = $totalPurchases * ($coupon->discount_value / 100);
                    }
                    
                    // Цвета для уровней лояльности
                    $loyaltyColors = [
                        'Platinum' => '#a855f7',
                        'Gold' => '#eab308',
                        'Silver' => '#94a3b8',
                        'Bronze' => '#cd7f32',
                        'Standard' => '#64748b'
                    ];
                    $loyaltyColor = $loyaltyColors[$loyaltyLevel] ?? '#64748b';
                @endphp
                <tr>
                    <td data-label="Имя" style="padding: 12px 16px;">
                        <div style="font-weight: 500;">{{ $customer->full_name ?? '—' }}</div>
                        @if($profile)
                            <span class="badge" style="background: {{ $loyaltyColor }}20; color: {{ $loyaltyColor }}; font-size: 11px; margin-top: 4px;">
                                <i class="bi bi-star-fill"></i> {{ $loyaltyLevel }}
                            </span>
                        @endif
                    </td>
                    <td data-label="Телефон" style="padding: 12px 16px; color: #94a3b8;">{{ $customer->phone ?? '—' }}</td>
                    <td data-label="Покупки" style="padding: 12px 16px;">
                        <div style="font-weight: 600; color: #10b981;">
                            {{ number_format($totalPurchases, 0, '.', ' ') }} ₽
                        </div>
                        <div style="font-size: 12px; color: #64748b;">
                            {{ $purchaseCount }} {{ trans_choice('раз|раза|раз', $purchaseCount) }}
                        </div>
                    </td>
                    <td data-label="Сэкономлено" style="padding: 12px 16px;">
                        @if($totalSaved > 0)
                            <div style="font-weight: 600; color: #f59e0b;">
                                {{ number_format($totalSaved, 0, '.', ' ') }} ₽
                            </div>
                            <div style="font-size: 12px; color: #64748b;">
                                ~{{ round($coupon->discount_value) }}% от покупок
                            </div>
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td data-label="Тип скидки" style="padding: 12px 16px;">
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
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                Использовано: {{ $coupon->usage_count }} {{ trans_choice('раз|раза|раз', $coupon->usage_count) }}
                            </div>
                        @else
                            <span style="color: #64748b;">—</span>
                        @endif
                    </td>
                    <td data-label="QR код" style="padding: 12px 16px;">
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
                    <td data-label="Статус" style="padding: 12px 16px;">
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
                    <td data-label="Действия" style="padding: 12px 16px;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button class="btn btn-outline btn-info btn-details"
                                data-full-name="{{ $customer->full_name ?? '' }}"
                                data-phone="{{ $customer->phone ?? '' }}"
                                data-total-purchases="{{ $totalPurchases ?? 0 }}"
                                data-purchase-count="{{ $purchaseCount ?? 0 }}"
                                data-loyalty-level="{{ $loyaltyLevel ?? 'Standard' }}"
                                data-has-coupon="{{ $hasCoupon ? '1' : '0' }}"
                                data-discount-type="{{ $hasCoupon ? ($coupon->discount_type ?? '') : '' }}"
                                data-discount-value="{{ $hasCoupon ? ($coupon->discount_value ?? 0) : 0 }}"
                                data-usage-count="{{ $hasCoupon ? ($coupon->usage_count ?? 0) : 0 }}"
                                data-is-active="{{ $hasCoupon ? ($coupon->is_active ? '1' : '0') : '0' }}"
                                data-valid-until="{{ $hasCoupon && $coupon->valid_until ? $coupon->valid_until->format('d.m.Y') : '' }}"
                                data-created-at="{{ $customer->created_at ? $customer->created_at->format('d.m.Y H:i') : '' }}"
                                title="Подробная информация">
                                <i class="bi bi-info-circle"></i>
                            </button>
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
                                    title="Удалить клиента" style="color: #ef4444; border-color: #ef4444;">
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
                    <td colspan="8" style="text-align: center; padding: 3rem; color: #64748b;">Клиенты не найдены</td>
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
<div id="createModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="bi bi-plus-circle"></i> Создать купонную карту</h3>
                <button type="button" class="btn-close" onclick="closeCreateModal()">&times;</button>
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
</div>

{{-- Модальное окно редактирования купона --}}
<div id="editModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="bi bi-pencil"></i> Редактировать купонную карту</h3>
                <button type="button" class="btn-close" onclick="closeEditModal()">&times;</button>
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
</div>

{{-- Модальное окно просмотра QR кода --}}
<div id="qrModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content" style="text-align: center;">
            <div class="modal-header">
                <h3 class="modal-title"><i class="bi bi-qr-code"></i> QR код клиента</h3>
                <button type="button" class="btn-close" onclick="closeQrModal()">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p id="qrClientName" style="font-weight: 600; margin-bottom: 16px;"></p>
                <div id="qrCodeContainer" style="background: white; padding: 24px; border-radius: 12px; display: inline-block;"></div>
                <p style="margin-top: 16px; color: #64748b; font-size: 14px;">Покажите этот QR код клиенту для сканирования</p>
            </div>
        </div>
    </div>
</div>

{{-- Модальное окно просмотра деталей клиента --}}
<div id="detailsModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="bi bi-person-badge"></i> Информация о клиенте</h3>
                <button type="button" class="btn-close" onclick="closeDetailsModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background: rgba(15, 23, 42, 0.5); border-radius: 12px; padding: 20px;">
                    <!-- Основная информация -->
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #f1f5f9; margin: 0 0 12px 0; font-size: 18px;">
                            <i class="bi bi-person" style="color: #38bdf8;"></i> <span id="detailsName"></span>
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Телефон</div>
                                <div style="color: #f1f5f9; font-weight: 500;"><i class="bi bi-phone"></i> <span id="detailsPhone"></span></div>
                            </div>
                            <div>
                                <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Дата регистрации</div>
                                <div style="color: #f1f5f9; font-weight: 500;"><i class="bi bi-calendar"></i> <span id="detailsCreatedAt"></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика покупок -->
                    <div style="border-top: 1px solid rgba(148, 163, 184, 0.1); padding-top: 20px; margin-bottom: 20px;">
                        <h5 style="color: #e2e8f0; margin: 0 0 12px 0; font-size: 16px;">
                            <i class="bi bi-cart3" style="color: #10b981;"></i> Статистика покупок
                        </h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div style="background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 8px;">
                                <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">Всего покупок</div>
                                <div style="color: #10b981; font-weight: 700; font-size: 18px;" id="detailsTotalPurchases"></div>
                            </div>
                            <div style="background: rgba(56, 189, 248, 0.1); padding: 12px; border-radius: 8px;">
                                <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">Количество</div>
                                <div style="color: #38bdf8; font-weight: 700; font-size: 18px;" id="detailsPurchaseCount"></div>
                            </div>
                        </div>
                        <div style="margin-top: 12px;">
                            <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Уровень лояльности</div>
                            <div id="detailsLoyaltyLevel" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; font-weight: 600;"></div>
                        </div>
                    </div>

                    <!-- Информация о купоне -->
                    <div style="border-top: 1px solid rgba(148, 163, 184, 0.1); padding-top: 20px;">
                        <h5 style="color: #e2e8f0; margin: 0 0 12px 0; font-size: 16px;">
                            <i class="bi bi-ticket-perforated" style="color: #f59e0b;"></i> Купонная карта
                        </h5>
                        <div id="detailsCouponInfo">
                            <!-- Заполняется через JS -->
                        </div>
                    </div>

                    <!-- Сэкономлено -->
                    <div style="border-top: 1px solid rgba(148, 163, 184, 0.1); padding-top: 20px; margin-top: 20px;">
                        <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(239, 68, 68, 0.15)); padding: 16px; border-radius: 12px; border: 1px solid rgba(245, 158, 11, 0.3);">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">💰 Всего сэкономил</div>
                                    <div style="color: #f59e0b; font-weight: 700; font-size: 24px;" id="detailsTotalSaved"></div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Средний % скидки</div>
                                    <div style="color: #f59e0b; font-weight: 600; font-size: 20px;" id="detailsAvgDiscount"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeDetailsModal()">Закрыть</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
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
    .btn-info {
        color: #38bdf8;
        border-color: #38bdf8;
    }
    .btn-info:hover {
        background: rgba(56, 189, 248, 0.1);
    }
    
    /* Фикс для мобильной прокрутки */
    @media (max-width: 768px) {
        body, html {
            overflow-x: hidden;
            max-width: 100vw;
        }
        .table {
            font-size: 13px;
        }
        .table th, .table td {
            padding: 8px 12px !important;
            white-space: nowrap;
        }
        .table thead {
            position: sticky;
            top: 0;
            background: #0f172a;
            z-index: 10;
        }
    }
</style>
@endpush

@push('scripts')
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
    const modalEl = document.getElementById('createModal');
    const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    instance.show();
}

function closeCreateModal() {
    const modalEl = document.getElementById('createModal');
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) instance.hide();
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
    
    const modalEl = document.getElementById('editModal');
    const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    instance.show();
    toggleEditDiscountValue();
}

function closeEditModal() {
    const modalEl = document.getElementById('editModal');
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) instance.hide();
}

function toggleEditDiscountValue() {
    const type = document.getElementById('editDiscountType').value;
    document.getElementById('editDiscountValueGroup').style.display = type === 'percent' ? 'block' : 'none';
}

function showQrCode(qrData, clientName) {
    document.getElementById('qrClientName').textContent = clientName || 'Клиент';
    const container = document.getElementById('qrCodeContainer');
    container.innerHTML = '<div style="padding: 20px;"><i class="bi bi-hourglass-split"></i> Генерирую...</div>';
    
    try {
        const data = qrData;
        
        if (!data || !data.coupon_id || !data.code) {
            throw new Error('Неверный формат данных');
        }
        
        const qrText = data.coupon_id + ':' + data.code;
        const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrText)}&color=0f172a&margin=10`;
        
        container.innerHTML = `
            <img src="${qrImageUrl}" alt="QR Code" style="max-width: 100%; height: auto;" 
                 onload="this.parentElement.style.padding='0'"
                 onerror="this.parentElement.innerHTML='<p style=\'color: #ef4444;\'>Ошибка загрузки QR</p>'">
        `;
    } catch (e) {
        container.innerHTML = '<p style="color: #ef4444;">Ошибка: ' + e.message + '</p>';
    }
    
    const modalEl = document.getElementById('qrModal');
    const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    instance.show();
}

function closeQrModal() {
    const modalEl = document.getElementById('qrModal');
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) instance.hide();
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

    // Кнопки просмотра деталей клиента
    document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('click', function() {
            showCustomerDetails({
                full_name: this.dataset.fullName,
                phone: this.dataset.phone,
                total_purchases: parseFloat(this.dataset.totalPurchases) || 0,
                purchase_count: parseInt(this.dataset.purchaseCount) || 0,
                loyalty_level: this.dataset.loyaltyLevel,
                has_coupon: this.dataset.hasCoupon === '1',
                discount_type: this.dataset.discountType,
                discount_value: parseFloat(this.dataset.discountValue) || 0,
                usage_count: parseInt(this.dataset.usageCount) || 0,
                is_active: this.dataset.isActive === '1',
                valid_until: this.dataset.validUntil,
                created_at: this.dataset.createdAt
            });
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

// Показать детали клиента
function showCustomerDetails(data) {
    // Основная информация
    document.getElementById('detailsName').textContent = data.full_name || '—';
    document.getElementById('detailsPhone').textContent = data.phone || '—';
    document.getElementById('detailsCreatedAt').textContent = data.created_at || '—';

    // Статистика покупок
    document.getElementById('detailsTotalPurchases').textContent = new Intl.NumberFormat('ru-RU').format(data.total_purchases) + ' ₽';
    document.getElementById('detailsPurchaseCount').textContent = data.purchase_count + ' раз';

    // Уровень лояльности
    const loyaltyColors = {
        'Platinum': '#a855f7',
        'Gold': '#eab308',
        'Silver': '#94a3b8',
        'Bronze': '#cd7f32',
        'Standard': '#64748b'
    };
    const loyaltyEl = document.getElementById('detailsLoyaltyLevel');
    const color = loyaltyColors[data.loyalty_level] || '#64748b';
    loyaltyEl.style.background = color + '20';
    loyaltyEl.style.color = color;
    loyaltyEl.innerHTML = '<i class="bi bi-star-fill"></i> ' + data.loyalty_level;

    // Расчет сэкономленного
    const totalSaved = data.discount_type === 'percent' && data.discount_value
        ? data.total_purchases * (data.discount_value / 100)
        : 0;
    document.getElementById('detailsTotalSaved').textContent = new Intl.NumberFormat('ru-RU').format(Math.round(totalSaved)) + ' ₽';
    document.getElementById('detailsAvgDiscount').textContent = data.discount_value ? data.discount_value + '%' : '—';

    // Информация о купоне
    const couponInfoEl = document.getElementById('detailsCouponInfo');
    if (data.has_coupon) {
        const discountTypeText = data.discount_type === 'percent' ? `Процентная (${data.discount_value}%)` : 'Оптовая (закупочная цена)';
        const statusText = data.is_active ? '<span style="color: #10b981;"><i class="bi bi-check-circle"></i> Активен</span>' : '<span style="color: #ef4444;"><i class="bi bi-x-circle"></i> Неактивен</span>';
        const validUntilText = data.valid_until ? `<div style="color: #64748b; font-size: 12px; margin-top: 8px;">Действует до: <span style="color: #f1f5f9;">${data.valid_until}</span></div>` : '';
        
        couponInfoEl.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Тип скидки</div>
                    <div style="color: #f1f5f9; font-weight: 500;">${discountTypeText}</div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Статус</div>
                    <div>${statusText}</div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Использовано</div>
                    <div style="color: #f1f5f9; font-weight: 500;">${data.usage_count} раз</div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">Срок действия</div>
                    <div style="color: #f1f5f9; font-weight: 500;">${data.valid_until || 'Бессрочно'}</div>
                </div>
            </div>
            ${validUntilText}
        `;
    } else {
        couponInfoEl.innerHTML = '<div style="color: #64748b; text-align: center; padding: 12px;"><i class="bi bi-ticket-perforated"></i> Купонная карта не создана</div>';
    }

    // Открываем модальное окно
    const modalEl = document.getElementById('detailsModal');
    const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    instance.show();
}

function closeDetailsModal() {
    const modalEl = document.getElementById('detailsModal');
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) instance.hide();
}

// Инициализация
toggleDiscountValue();
</script>
@endpush
