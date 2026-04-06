@extends('layouts.owner')

@section('page-title', 'Отчёт по расходам')
@section('page-subtitle', 'Контроль операционных затрат и издержек')

@section('page-actions')
    <div class="flex flex-col md:flex-row gap-3">
        <button onclick="openCreateModal()" 
            class="btn glass bg-accent/20 hover:bg-accent/30 text-accent px-4 py-2 rounded-xl flex items-center gap-2 transition-all border border-accent/30">
            <i class="bi bi-plus-circle"></i>
            <span>Добавить расход</span>
        </button>
        <a href="{{ route('owner.reports.index') }}" class="btn glass bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl flex items-center justify-center gap-2 transition-all border border-white/10">
            <i class="bi bi-arrow-left"></i>
            <span>Назад</span>
        </a>
    </div>
@endsection

@section('content')

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="space-y-6">
    <!-- Total Expenses Card -->
    <div class="glass p-8 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 group">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 group-hover:scale-110 transition-transform">
                <i class="bi bi-receipt-cutoff text-4xl"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-muted uppercase tracking-widest">Общие расходы за период</p>
                <h3 class="text-4xl font-black text-rose-500 mt-1">{{ number_format($totalAmount, 0, '.', ' ') }} <span class="text-xl font-medium opacity-60">сом</span></h3>
            </div>
        </div>
        <div class="hidden md:block h-12 w-px bg-white/10"></div>
        <div class="flex gap-4">
            <div class="text-center">
                <p class="text-[10px] text-muted uppercase font-bold">Активных статей</p>
                <p class="text-xl font-bold text-white">{{ count($categories) }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] text-muted uppercase font-bold">Транзакций</p>
                <p class="text-xl font-bold text-white">{{ $expenses->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Expenses Details Table -->
    <div class="glass rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
            <h3 class="font-bold text-white flex items-center gap-2 uppercase tracking-tight text-sm">
                <i class="bi bi-list-check text-rose-500"></i>
                Детализация расходов
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left responsive-table">
                <thead>
                    <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-muted">
                        <th class="px-6 py-4">№</th>
                        <th class="px-6 py-4">Дата</th>
                        <th class="px-6 py-4">Категория</th>
                        <th class="px-6 py-4">Описание</th>
                        <th class="px-6 py-4">Магазин</th>
                        <th class="px-6 py-4 text-right">Сумма</th>
                        <th class="px-6 py-4 text-right">Автор</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4" data-label="№">
                                <span class="text-[10px] font-mono text-muted">#{{ $expense->id }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-white/90" data-label="Дата">
                                {{ $expense->expense_date->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4" data-label="Категория">
                                @if($expense->category === 'rent') 
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-bold border border-amber-500/20">Аренда</span>
                                @elseif($expense->category === 'utilities') 
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-500 text-[10px] font-bold border border-cyan-500/20">Коммуналка</span>
                                @elseif($expense->category === 'salary') 
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold border border-emerald-500/20">Зарплата</span>
                                @elseif($expense->category === 'taxes') 
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-500 text-[10px] font-bold border border-rose-500/20">Налоги</span>
                                @elseif($expense->category === 'marketing') 
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">Маркетинг</span>
                                @else 
                                    <span class="px-2 py-0.5 rounded-full bg-white/10 text-white/60 text-[10px] font-bold border border-white/20">{{ ucfirst($expense->category) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-muted max-w-[200px] truncate" data-label="Описание">
                                {{ $expense->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4" data-label="Магазин">
                                <span class="text-xs text-white/80">{{ $expense->shop?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Сумма">
                                <span class="font-black text-rose-500 text-sm">{{ number_format($expense->amount, 0, '.', ' ') }} сом</span>
                            </td>
                            <td class="px-6 py-4 text-right" data-label="Автор">
                                <span class="text-[10px] text-muted italic">{{ $expense->user?->username ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="bi bi-wallet2 text-5xl mb-4 opacity-10"></i>
                                    <p class="text-sm font-medium">Расходы не найдены за этот период</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Модальное окно добавления расхода --}}
<div id="createExpenseModal" class="fixed inset-0 z-[999] hidden overflow-y-auto" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="glass p-8 rounded-2xl w-full max-w-lg relative border border-white/10">
            <button onclick="closeCreateModal()" class="absolute top-4 right-4 text-muted hover:text-white text-2xl">
                <i class="bi bi-x-lg"></i>
            </button>
            
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-500">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                Добавить расход
            </h3>
            
            <form method="POST" action="{{ route('owner.reports.expenses.store') }}">
                @csrf
                
                <div class="space-y-4">
                    <!-- Дата -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Дата расхода *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required
                            style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 10px 14px; color: #f8fafc; font-size: 14px; color-scheme: dark;">
                    </div>
                    
                    <!-- Категория -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Категория *</label>
                        <select name="category" required
                            style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 10px 14px; color: #f8fafc; font-size: 14px;">
                            <option value="">Выберите категорию...</option>
                            <option value="rent">🏠 Аренда</option>
                            <option value="utilities">💧 Коммунальные услуги</option>
                            <option value="salary">💰 Зарплата</option>
                            <option value="taxes">📋 Налоги</option>
                            <option value="marketing">📢 Маркетинг</option>
                            <option value="supplies">🛒 Расходные материалы</option>
                            <option value="maintenance">🔧 Обслуживание</option>
                            <option value="other">📦 Другое</option>
                        </select>
                    </div>
                    
                    <!-- Сумма -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Сумма (сом) *</label>
                        <input type="number" name="amount" required min="1" step="0.01" placeholder="0.00"
                            style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 10px 14px; color: #f8fafc; font-size: 14px;">
                    </div>
                    
                    <!-- Магазин -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Магазин</label>
                        <select name="shop_id"
                            style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 10px 14px; color: #f8fafc; font-size: 14px;">
                            <option value="">Не указан / Общий расход</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Описание -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Описание</label>
                        <textarea name="description" rows="3" placeholder="Например: Аренда за апрель..."
                            style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 10px 14px; color: #f8fafc; font-size: 14px; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <!-- Кнопки -->
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeCreateModal()"
                        style="flex: 1; background: rgba(255, 255, 255, 0.05); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.1); padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer;">
                        Отмена
                    </button>
                    <button type="submit"
                        style="flex: 1; background: #ef4444; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                        <i class="bi bi-plus-circle"></i> Добавить расход
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCreateModal() {
    document.getElementById('createExpenseModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createExpenseModal').classList.add('hidden');
}
</script>
@endpush
