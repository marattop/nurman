@extends('layouts.owner')

@section('page-title', 'Аналитика')
@section('page-subtitle', 'Полный анализ вашего бизнеса')

@section('content')

<!-- КАЛЕНДАРЬ -->
<div class="glass p-6 rounded-2xl mb-6 border border-accent/20">
    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.1em;">
                <i class="bi bi-calendar3" style="color: #a855f7;"></i> Дата ОТ
            </label>
            <input type="date" id="date_from" value="{{ $dateFrom }}" 
                style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 12px 16px; color: #f8fafc; font-size: 15px; box-sizing: border-box; color-scheme: dark;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.1em;">
                <i class="bi bi-calendar3" style="color: #a855f7;"></i> Дата ДО
            </label>
            <input type="date" id="date_to" value="{{ $dateTo }}" 
                style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 12px 16px; color: #f8fafc; font-size: 15px; box-sizing: border-box; color-scheme: dark;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="applyFilter()" 
                style="background: #a855f7; color: #0f172a; border: none; padding: 12px 28px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);">
                <i class="bi bi-funnel"></i> Применить
            </button>
        </div>
    </div>
</div>

<!-- Средний чек -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass p-5 rounded-2xl">
        <p class="text-muted text-xs font-bold uppercase tracking-wider mb-2">Средний чек</p>
        <p class="text-3xl font-extrabold text-accent">{{ number_format($avgCheck->avg ?? 0, 0, '.', ' ') }} сом</p>
    </div>
    <div class="glass p-5 rounded-2xl">
        <p class="text-muted text-xs font-bold uppercase tracking-wider mb-2">Минимальный</p>
        <p class="text-3xl font-extrabold text-warning">{{ number_format($avgCheck->min ?? 0, 0, '.', ' ') }} сом</p>
    </div>
    <div class="glass p-5 rounded-2xl">
        <p class="text-muted text-xs font-bold uppercase tracking-wider mb-2">Максимальный</p>
        <p class="text-3xl font-extrabold text-success">{{ number_format($avgCheck->max ?? 0, 0, '.', ' ') }} сом</p>
    </div>
</div>

<!-- Продажи по часам -->
<div class="glass p-6 rounded-2xl mb-6">
    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <i class="bi bi-clock text-accent"></i> Продажи по часам
    </h3>
    <div class="grid grid-cols-12 gap-2">
        @for($h = 0; $h < 24; $h++)
            @php
                $hourData = $salesByHour->firstWhere('hour', $h);
                $count = $hourData ? $hourData->count : 0;
                $maxCount = $salesByHour->max('count') ?: 1;
                $height = ($count / $maxCount) * 100;
            @endphp
            <div class="text-center">
                <div class="bg-accent/20 rounded-t" style="height: {{ max($height, 4) }}px;"></div>
                <p class="text-xs text-muted mt-1">{{ $h }}</p>
                @if($count > 0)
                    <p class="text-xs font-bold text-accent">{{ $count }}</p>
                @endif
            </div>
        @endfor
    </div>
</div>

<!-- Продажи по дням недели -->
<div class="glass p-6 rounded-2xl mb-6">
    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <i class="bi bi-calendar-week text-accent"></i> Продажи по дням недели
    </h3>
    <div class="space-y-3">
        @foreach($salesByDay as $day)
            @php
                $maxRevenue = $salesByDay->max('revenue') ?: 1;
                $width = ($day->revenue / $maxRevenue) * 100;
            @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-bold">{{ $day->day_name }}</span>
                    <span class="text-accent font-bold">{{ number_format($day->revenue, 0, '.', ' ') }} сом ({{ $day->count }})</span>
                </div>
                <div class="bg-white/5 rounded-full h-3">
                    <div class="bg-accent h-3 rounded-full" style="width: {{ $width }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Топ продавцов -->
<div class="glass p-6 rounded-2xl mb-6">
    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <i class="bi bi-person-badge text-accent"></i> Топ продавцов
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase">#</th>
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase">Продавец</th>
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase text-right">Продажи</th>
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase text-right">Выручка</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSellers as $index => $seller)
                <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                    <td class="px-4 py-3 font-bold text-accent">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $seller->full_name ?: $seller->username }}</td>
                    <td class="px-4 py-3 text-right">{{ $seller->sales_count }}</td>
                    <td class="px-4 py-3 text-right font-bold text-success">{{ number_format($seller->total_revenue, 0, '.', ' ') }} сом</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Топ магазинов -->
<div class="glass p-6 rounded-2xl mb-6">
    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <i class="bi bi-shop text-accent"></i> Топ магазинов
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase">#</th>
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase">Магазин</th>
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase text-right">Продажи</th>
                    <th class="px-4 py-3 text-xs font-bold text-muted uppercase text-right">Выручка</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topShops as $index => $shop)
                <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                    <td class="px-4 py-3 font-bold text-accent">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $shop->name }}</td>
                    <td class="px-4 py-3 text-right">{{ $shop->sales_count }}</td>
                    <td class="px-4 py-3 text-right font-bold text-success">{{ number_format($shop->total_revenue, 0, '.', ' ') }} сом</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function applyFilter() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    if (!dateFrom || !dateTo) {
        alert('Укажите обе даты!');
        return;
    }
    window.location.href = `?date_from=${dateFrom}&date_to=${dateTo}`;
}
</script>
@endpush
