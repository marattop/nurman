@extends('layouts.warehouse')

@section('page-title', 'Ожидающие списания')
@section('page-subtitle', 'Списания, ожидающие подтверждения')

@section('content')
<div class="glass rounded-[2rem] overflow-hidden border border-white/5 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left responsive-table">
            <thead>
                <tr class="bg-white/[0.03] text-[10px] font-black uppercase tracking-widest text-muted">
                    <th class="px-6 py-5">№</th>
                    <th class="px-6 py-5">Дата</th>
                    <th class="px-6 py-5">Причина</th>
                    <th class="px-6 py-5 text-center">Товаров</th>
                    <th class="px-6 py-5 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($writeoffs ?? [] as $writeoff)
                <tr class="hover:bg-white/[0.02]">
                    <td class="px-6 py-4">
                        <div class="font-bold text-white text-sm">#{{ $writeoff->id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-white text-sm">{{ $writeoff->created_at->format('d.m.Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-white text-sm">{{ $writeoff->reason ?? 'Брак' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white text-sm">{{ $writeoff->items_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="btn btn-success btn-sm px-4 py-2 rounded-xl text-xs font-black">
                            <i class="bi bi-check-lg"></i> УТВЕРДИТЬ
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="text-muted">
                            <i class="bi bi-inbox text-3xl mb-3"></i>
                            <p class="text-sm">Нет ожидающих списаний</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
