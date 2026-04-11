@extends('common.layout')
@section('title', 'Team Structure')
@section('main')

{{-- 1. Ensure Alpine.js is loaded for the toggle logic --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>
    /* Prevents children from "flashing" on page load before Alpine.js initializes */
    [x-cloak] { display: none !important; }

    .mobile-view { display: none; padding: 15px; background: #f4f7f6; min-height: 100vh; }
    .desktop-view { display: block; }

    @media (max-width: 991px) {
        .mobile-view { display: block; }
        .desktop-view { display: none; }
    }

    .mobile-card {
        background: #fff;
        border-radius: 12px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .mobile-card:active { transform: scale(0.98); background: #f8fafc; }
</style>

@php
    if (!function_exists('renderMobileNested')) {
        function renderMobileNested($item, $side = 'Root') {
            if (!$item) return '';

            $isActive = $item['is_active'] ?? false;
            $image = $isActive ? asset('assets/images/g1.png') : asset('assets/images/r1.png');
            $hasChildren = !empty($item['left']) || !empty($item['right']);
            $pkgAmount = number_format($item['personal_investment'] ?? 0, 2);
            
            // open: false ensures it starts CLOSED
            $html = '<div class="mobile-node-group" x-data="{ open: false }" style="margin-bottom:10px;">';
            
            $html .= '<div class="mobile-card" @click="open = !open" style="cursor:pointer; position:relative;">';
            
            if($side !== 'Root') {
                $sideColor = ($side == 'Left') ? '#00cec9' : '#ff7675';
                $html .= '<span style="position:absolute; top:-8px; left:12px; background:'.$sideColor.'; font-size:9px; padding:2px 8px; border-radius:4px; font-weight:900; color:#000;">'.$side.'</span>';
            }

            $html .= '<img src="'.$image.'" style="width:45px; height:45px; border-radius:50%;">';
            $html .= '<div style="display:flex; flex-direction:column; flex-grow:1;">
                        <span style="font-size:10px; color:#3b82f6; font-weight:800;">'.$item['member_id'].'</span>
                        <span style="font-size:14px; font-weight:700; color:#1e293b; text-transform:uppercase;">'.$item['username'].'</span>
                        <span style="font-size:11px; color:#64748b;">Pkg: $'.$pkgAmount.'</span>
                      </div>';
            
            if($hasChildren) {
                $html .= '<i class="fa fa-chevron-down" :style="open ? \'transform:rotate(180deg)\' : \'\'" style="transition:0.3s; color:#cbd5e1; font-size:12px;"></i>';
            }

            $html .= '</div>';

            // x-show="open" handles the visibility
            if($hasChildren) {
                $html .= '<div x-show="open" x-cloak x-collapse style="margin-left:20px; padding-left:15px; border-left:2px solid #e2e8f0; margin-top:8px;">';
                $html .= renderMobileNested($item['left'] ?? null, 'Left');
                $html .= renderMobileNested($item['right'] ?? null, 'Right');
                $html .= '</div>';
            }

            $html .= '</div>';
            return $html;
        }
    }
@endphp

{{-- DESKTOP UI --}}
<div class="desktop-view">
    @include('partials.tree-node', ['node' => $tree])
</div>

{{-- MOBILE UI --}}
<div class="mobile-view">
    {{-- Summary Stats --}}
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <div style="flex:1; background:#1e293b; color:#fff; padding:12px; border-radius:10px;">
            <div style="font-size:9px; opacity:0.6;">LEFT BIZ</div>
            <div style="font-size:14px; font-weight:800;">${{ number_format($tree['total_business_left'] ?? 0, 2) }}</div>
        </div>
        <div style="flex:1; background:#1e293b; color:#fff; padding:12px; border-radius:10px; text-align:right;">
            <div style="font-size:9px; opacity:0.6;">RIGHT BIZ</div>
            <div style="font-size:14px; font-weight:800;">${{ number_format($tree['total_business_right'] ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- The Tree Nodes --}}
    <div class="mobile-tree-root">
        @if(isset($tree))
            {!! renderMobileNested($tree) !!}
        @else
            <p style="text-align:center; color:#94a3b8;">No data found.</p>
        @endif
    </div>
</div>

@endsection