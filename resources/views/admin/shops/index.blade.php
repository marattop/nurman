@extends('layouts.admin')
@section('page-title', 'Магазины')
@section('content')
<div class="page-header">
    <div class="page-icon"><i class="bi bi-shop"></i></div>
    <div>
        <h1 class="page-title">Магазины</h1>
        <p class="page-subtitle">Управление магазинами</p>
    </div>
</div>

@if(session('success'))
    <div class="card" style="padding: 16px; border: 1px solid rgba(16,185,129,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#10b981; margin-bottom: 6px;">
            <i class="bi bi-check-circle"></i> Успешно
        </div>
        <div style="color:#f1f5f9;">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="card" style="padding: 16px; border: 1px solid rgba(239,68,68,0.25); margin-bottom: 16px;">
        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
            <i class="bi bi-x-circle"></i> Ошибка
        </div>
        <div style="color:#f1f5f9;">{{ session('error') }}</div>
    </div>
@endif

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header">
        <h3 class="card-header-title"><i class="bi bi-list-ul" style="color: #38bdf8;"></i> Список магазинов</h3>
        <button type="button" class="btn btn-primary" onclick="showAddShopModal()">
            <i class="bi bi-plus-lg"></i> Добавить
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Название</th>
                    <th>Адрес</th>
                    <th>Телефон</th>
                    <th>Продавцы</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                    @php
                        $isMain = isset($mainShop) && $mainShop && (int)$mainShop->id === (int)$shop->id;
                    @endphp
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $shop->name }}</div>
                        @if($isMain)
                            <div style="margin-top:4px;">
                                <span class="badge badge-blue">Основной</span>
                            </div>
                        @endif
                    </td>
                    <td style="color: #94a3b8;">{{ $shop->address ?? '—' }}</td>
                    <td style="color: #94a3b8;">{{ $shop->phone ?? '—' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $shop->sellers_count ?? 0 }} чел.</span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-outline"
                            title="Редактировать"
                            onclick="openEditShopModal({
                                id: {{ $shop->id }},
                                name: @js($shop->name),
                                code: @js($shop->code),
                                address: @js($shop->address),
                                phone: @js($shop->phone),
                                is_active: {{ $shop->is_active ? 'true' : 'false' }}
                            })"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>

                        @if($isMain)
                            <button
                                type="button"
                                class="btn btn-danger"
                                title="Удалить основной магазин (нужен перенос)"
                                onclick="openTransferMainShopModal({{ $shop->id }})"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        @else
                            <form action="{{ route('admin.shops.delete', $shop) }}" method="POST" style="display:inline;" onsubmit="return confirm('Удалить магазин? Если есть связанные данные — он будет деактивирован.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">Магазины не найдены</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add shop modal --}}
<div id="addShopModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><i class="bi bi-plus-circle"></i> Добавить магазин</div>
                <button type="button" class="btn-close" onclick="hideAddShopModal()">&times;</button>
            </div>
            <div class="modal-body">
                @if($errors->any())
                    <div style="margin-bottom: 12px; padding: 12px; border-radius: 12px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);">
                        <div style="font-weight:700; color:#ef4444; margin-bottom: 6px;">
                            <i class="bi bi-exclamation-triangle"></i> Проверьте поля
                        </div>
                        <ul style="margin:0; padding-left: 18px; color:#f1f5f9; font-size:14px;">
                            @foreach($errors->all() as $error)
                                <li style="margin: 4px 0;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.shops.store') }}" method="POST">
                    @csrf
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Название *</label>
                            <input class="input" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Code *</label>
                            <input class="input" type="text" name="code" value="{{ old('code') }}" required maxlength="50">
                        </div>
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Адрес</label>
                        <input class="input" type="text" name="address" value="{{ old('address') }}" maxlength="65535">
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Телефон</label>
                        <input class="input" type="text" name="phone" value="{{ old('phone') }}" maxlength="20">
                    </div>

                    <div style="margin-top:12px; display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_active" value="1" id="addShopIsActive" {{ old('is_active') ? 'checked' : '' }}>
                        <label for="addShopIsActive" style="font-weight:700; cursor:pointer; color:#f1f5f9;">Активен</label>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                            <i class="bi bi-save"></i> Создать
                        </button>
                        <button type="button" class="btn btn-outline" onclick="hideAddShopModal()" style="flex:1; justify-content:center;">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit shop modal --}}
<div id="editShopModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><i class="bi bi-pencil"></i> Редактировать магазин</div>
                <button type="button" class="btn-close" onclick="hideEditShopModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editShopForm" action="#" method="POST">
                    @csrf
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Название *</label>
                            <input class="input" type="text" name="name" id="editShopName" value="" required maxlength="255">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Code *</label>
                            <input class="input" type="text" name="code" id="editShopCode" value="" required maxlength="50">
                        </div>
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Адрес</label>
                        <input class="input" type="text" name="address" id="editShopAddress" value="" maxlength="65535">
                    </div>

                    <div style="margin-top:12px;">
                        <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Телефон</label>
                        <input class="input" type="text" name="phone" id="editShopPhone" value="" maxlength="20">
                    </div>

                    <div style="margin-top:12px; display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_active" value="1" id="editShopIsActive">
                        <label for="editShopIsActive" style="font-weight:700; cursor:pointer; color:#f1f5f9;">Активен</label>
                    </div>

                    <div style="margin-top: 18px; display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                            <i class="bi bi-save"></i> Сохранить
                        </button>
                        <button type="button" class="btn btn-outline" onclick="hideEditShopModal()" style="flex:1; justify-content:center;">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Transfer main shop modal --}}
<div id="transferMainShopModal" class="modal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><i class="bi bi-arrow-left-right"></i> Перенос в новый магазин</div>
                <button type="button" class="btn-close" onclick="hideTransferMainShopModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 12px; color:#f1f5f9;">
                    Основной магазин нельзя удалить без переноса сотрудников и остатков в другой магазин.
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display:block; font-weight:600; color:#94a3b8; margin-bottom:6px;">Перенести в магазин</label>
                    <select id="transferToShopSelect" class="input" style="appearance:none;" onchange="onTransferSelectChange()">
                        @if(isset($mainShop) && $mainShop)
                            @foreach($shops as $s)
                                @if((int)$s->id !== (int)$mainShop->id)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="margin-bottom: 12px;">
                    <button type="button" class="btn btn-outline" style="width:100%; justify-content:center;" onclick="hideTransferMainShopModal(); showAddShopModal();">
                        <i class="bi bi-plus-lg"></i> Создать новый магазин
                    </button>
                </div>

                <form id="transferDeleteForm" action="#" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="transfer_to_shop_id" id="transferToShopId" value="">
                    <button type="button" id="confirmTransferDeleteBtn" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="submitTransferDelete()">
                        <i class="bi bi-trash"></i> Перенести и деактивировать
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddShopModal() {
        const modal = document.getElementById('addShopModal');
        if (!modal) return;
        modal.style.display = 'block';
        modal.classList.add('show');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop addshop-backdrop';
        backdrop.onclick = hideAddShopModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function hideAddShopModal() {
        const modal = document.getElementById('addShopModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.addshop-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }

    function openEditShopModal(shop) {
        const modal = document.getElementById('editShopModal');
        const form = document.getElementById('editShopForm');
        if (!modal || !form) return;

        document.getElementById('editShopName').value = shop.name ?? '';
        document.getElementById('editShopCode').value = shop.code ?? '';
        document.getElementById('editShopAddress').value = shop.address ?? '';
        document.getElementById('editShopPhone').value = shop.phone ?? '';
        document.getElementById('editShopIsActive').checked = !!shop.is_active;

        form.action = '{{ route('admin.shops.update', ['shop' => '__ID__']) }}'.replace('__ID__', shop.id);

        modal.style.display = 'block';
        modal.classList.add('show');

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop editshop-backdrop';
        backdrop.onclick = hideEditShopModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function hideEditShopModal() {
        const modal = document.getElementById('editShopModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.editshop-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }

    function openTransferMainShopModal(mainShopId) {
        const modal = document.getElementById('transferMainShopModal');
        const select = document.getElementById('transferToShopSelect');
        const form = document.getElementById('transferDeleteForm');
        const hidden = document.getElementById('transferToShopId');
        const btn = document.getElementById('confirmTransferDeleteBtn');
        if (!modal || !select || !form || !hidden || !btn) return;

        form.action = '{{ route('admin.shops.delete', ['shop' => '__ID__']) }}'.replace('__ID__', mainShopId);
        hidden.value = select.value;

        modal.style.display = 'block';
        modal.classList.add('show');

        btn.disabled = !select.value;

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop transfer-backdrop';
        backdrop.onclick = hideTransferMainShopModal;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    function onTransferSelectChange() {
        const select = document.getElementById('transferToShopSelect');
        const hidden = document.getElementById('transferToShopId');
        const btn = document.getElementById('confirmTransferDeleteBtn');
        if (!select || !hidden || !btn) return;
        hidden.value = select.value;
        btn.disabled = !select.value;
    }

    function submitTransferDelete() {
        const hidden = document.getElementById('transferToShopId');
        if (!hidden || !hidden.value) {
            alert('Выберите магазин для переноса.');
            return;
        }
        document.getElementById('transferDeleteForm').submit();
    }

    function hideTransferMainShopModal() {
        const modal = document.getElementById('transferMainShopModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.querySelectorAll('.transfer-backdrop').forEach(b => b.remove());
        document.body.style.overflow = '';
    }
</script>
@endsection
