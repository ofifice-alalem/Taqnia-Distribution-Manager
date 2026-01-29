@extends('layouts.app')

@section('title', 'التخفيضات المتاحة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-gift"></i> التخفيضات المتاحة</h2>
</div>

@if($promotions->isEmpty())
    <div class="empty-state-card">
        <i class="bi bi-gift"></i>
        <h4>لا توجد تخفيضات متاحة حالياً</h4>
        <p>سيتم عرض التخفيضات النشطة هنا عند توفرها</p>
    </div>
@else
    <div class="row">
        @foreach($promotions as $promotion)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="promotion-card">
                    <div class="promotion-badge">
                        <i class="bi bi-gift-fill"></i>
                        عرض نشط
                    </div>
                    
                    <div class="promotion-header">
                        <h5>{{ $promotion->product->name }}</h5>
                    </div>
                    
                    <div class="promotion-body">
                        <div class="promotion-offer">
                            <div class="offer-item">
                                <span class="offer-label">اشتري</span>
                                <span class="offer-value">{{ number_format($promotion->min_quantity) }}</span>
                            </div>
                            <div class="offer-arrow">
                                <i class="bi bi-arrow-left"></i>
                            </div>
                            <div class="offer-item">
                                <span class="offer-label">واحصل على</span>
                                <span class="offer-value free">{{ number_format($promotion->free_quantity) }}</span>
                                <span class="offer-label">مجاناً</span>
                            </div>
                        </div>
                        
                        <div class="promotion-dates">
                            <div class="date-item">
                                <i class="bi bi-calendar-check"></i>
                                <span>من: {{ $promotion->start_date->format('Y/m/d') }}</span>
                            </div>
                            <div class="date-item">
                                <i class="bi bi-calendar-x"></i>
                                <span>إلى: {{ $promotion->end_date->format('Y/m/d') }}</span>
                            </div>
                        </div>
                        
                        @php
                            $daysLeft = now()->diffInDays($promotion->end_date, false);
                        @endphp
                        
                        @if($daysLeft <= 3 && $daysLeft >= 0)
                            <div class="promotion-alert">
                                <i class="bi bi-clock-history"></i>
                                ينتهي خلال {{ $daysLeft }} {{ $daysLeft == 1 ? 'يوم' : 'أيام' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<style>
.empty-state-card {
    background: var(--bg-card);
    border: 2px dashed var(--border);
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    color: var(--text-muted);
}

.empty-state-card i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state-card h4 {
    color: var(--text-heading);
    margin-bottom: 8px;
}

.empty-state-card p {
    margin: 0;
    font-size: 14px;
}

.promotion-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}

.promotion-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.promotion-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 1;
}

.promotion-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 24px 20px;
    text-align: center;
}

.promotion-header h5 {
    color: white;
    margin: 0;
    font-size: 18px;
    font-weight: 700;
}

.promotion-body {
    padding: 24px 20px;
}

.promotion-offer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-bottom: 24px;
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 12px;
}

.offer-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.offer-label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
}

.offer-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--primary);
}

.offer-value.free {
    color: #28a745;
}

.offer-arrow {
    font-size: 24px;
    color: var(--text-muted);
}

.promotion-dates {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-main);
}

.date-item i {
    color: var(--primary);
}

.promotion-alert {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #856404;
    font-weight: 600;
}

.promotion-alert i {
    font-size: 16px;
}

@media (max-width: 768px) {
    .promotion-offer {
        flex-direction: column;
        gap: 12px;
    }
    
    .offer-arrow {
        transform: rotate(90deg);
    }
}
</style>
@endsection
