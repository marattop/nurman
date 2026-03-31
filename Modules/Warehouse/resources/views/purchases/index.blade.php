@extends('layouts.warehouse')

@section('page-title', 'Журнал приёмок')
@section('page-subtitle', 'История поступлений товаров на склад')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <a href="{{ route('warehouse.purchases.create') }}" class="btn btn-primary w-full sm:w-auto px-8 py-3 rounded-2xl gap-2 font-black shadow-glow">
        <i class="bi bi-plus-lg"></i> НОВАЯ ПРИЁМКА
    </a>
    <div class="glass p-2 rounded-2xl flex items-center gap-2 border-white/5 w-full sm:w-80">
        <i class="bi bi-calendar text-muted ml-3"></i>
        <input type="date" class="bg-transparent border-none text-white px-2 py-1 focus:ring-0 text-sm w-full cursor-pointer">
    </div>
</div>

<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">Накладная</th>
                    <th class="px-6 py-5">Дата</th>
                    <th class="px-6 py-5">Товар</th>
                    <th class="px-6 py-5 text-center">Кол-во</th>
                    <th class="px-6 py-5 text-right">Сумма</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td data-label="Накладная" class="px-6 py-4">
                        <span class="font-mono text-accent">#{{ $purchase->invoice_number ?? $purchase->id }}</span>
                    </td>
                    <td data-label="Дата" class="px-6 py-4 text-xs text-muted">
                        {{ $purchase->created_at->format('d.m.Y') }}
                        <div class="text-[9px] opacity-50">{{ $purchase->created_at->format('H:i') }}</div>
                    </td>
                    <td data-label="Товар" class="px-6 py-4">
                        <div class="font-bold text-white text-sm">{{ $purchase->variant?->product?->name ?? '—' }}</div>
                        <div class="text-[10px] text-muted">{{ $purchase->variant?->size }} {{ $purchase->variant?->color }}</div>
                    </td>
                    <td data-label="Кол-во" class="px-6 py-4 text-center">
                        <span class="badge bg-success/10 text-success font-black">+{{ $purchase->quantity }} шт</span>
                    </td>
                    <td data-label="Сумма" class="px-6 py-4 text-right">
                        <div class="font-bold text-white text-sm">{{ number_format($purchase->total_amount, 0, '.', ' ') }} сом</div>
                        <div class="text-[9px] text-muted">{{ number_format($purchase->supplier_price, 0, '.', ' ') }} за ед.</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center">
                        <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-muted/20 mx-auto mb-4">
                            <i class="bi bi-truck text-4xl"></i>
                        </div>
                        <p class="text-muted italic">Приёмок не найдено</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
    <div class="px-6 py-6 border-t border-white/5">
        {{ $purchases->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
