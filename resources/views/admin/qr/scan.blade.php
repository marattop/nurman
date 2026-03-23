@extends('layouts.app')
@section('title', 'QR-сканер')
@section('content')
<style>
:root { --accent-primary: #10b981; --accent-blue: #3b82f6; --accent-orange: #f59e0b; --accent-purple: #8b5cf6; --radius-lg: 24px; --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.08); }
:root, [data-theme="light"] { --bg-color: #F2F2F7; --text-main: #1C1C1E; --text-muted: #8E8E93; --border-color: rgba(0, 0, 0, 0.08); --glass-bg: rgba(255, 255, 255, 0.75); --glass-border: rgba(0, 0, 0, 0.05); }
[data-theme="dark"] { --bg-color: #000000; --text-main: #FFFFFF; --text-muted: rgba(255, 255, 255, 0.7); --border-color: rgba(255, 255, 255, 0.08); --glass-bg: rgba(20, 20, 20, 0.85); --glass-border: rgba(255, 255, 255, 0.1); }
body { background: var(--bg-color); color: var(--text-main); transition: all 0.3s; }
.scan-card { background: var(--glass-bg); backdrop-filter: blur(20px); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--glass-border); padding: 2rem; text-align: center; }
.video-container { max-width: 640px; margin: 0 auto; }
#scanner-video { width: 100%; border-radius: 12px; background: #000; }
.product-info { text-align: left; margin-top: 1.5rem; }
.product-info h3 { color: var(--text-main); margin-bottom: 0.5rem; }
.product-info p { color: var(--text-muted); }
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem; }
.summary-item { background: var(--glass-border); padding: 1rem; border-radius: 12px; text-align: center; }
.summary-value { font-size: 1.5rem; font-weight: 700; }
.summary-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
.btn { padding: 10px 20px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; margin: 5px; }
.btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
.btn-outline { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); }
code { background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 2px 6px; border-radius: 4px; }
.badge { background: rgba(59, 130, 246, 0.15); color: #60a5fa; padding: 4px 8px; border-radius: 6px; font-size: 12px; }
</style>
<div class="container-fluid py-4">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.25rem;"><i class="bi bi-qr-code-scan"></i></div>
        <div><h2 style="font-size: 28px; font-weight: 700; color: var(--text-main); margin: 0;">QR-сканер</h2><p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Сканирование QR-кодов товаров</p></div>
    </div>
    <div class="scan-card">
        <div class="video-container">
            <video id="scanner-video" autoplay playsinline></video>
        </div>
        <div style="margin-top: 1.5rem;">
            <button class="btn btn-primary" id="start-btn"><i class="bi bi-camera"></i> Start Camera</button>
            <button class="btn btn-outline" id="stop-btn" style="display: none;"><i class="bi bi-camera-off"></i> Stop</button>
        </div>
        <div id="scan-result" class="product-info" style="display: none;">
            <h3 id="product-name"></h3>
            <p>Артикул: <code id="product-article"></code></p>
            <div class="summary-grid">
                <div class="summary-item"><div class="summary-value" style="color: #3b82f6;" id="summary-showcase"></div><div class="summary-label">Витрина</div></div>
                <div class="summary-item"><div class="summary-value" style="color: #f59e0b;" id="summary-warehouse"></div><div class="summary-label">Склад</div></div>
                <div class="summary-item"><div class="summary-value" style="color: #8b5cf6;" id="summary-sold"></div><div class="summary-label">Продано</div></div>
            </div>
            <div style="margin-top: 1rem;"><span class="badge" id="product-sku"></span></div>
        </div>
    </div>
</div>
<script>
// QR сканер скрипт
</script>
@endsection
