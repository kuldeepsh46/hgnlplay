@extends('common.layout')
@section('title', 'Team Tree')
@section('main')

<style>
body{background:#0b0e12;font-family:Inter,sans-serif}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 0 20px rgba(0,0,0,.4)}
.tree-scroll{overflow-x:auto}
.tree-table{border-collapse:collapse;margin:auto}
.tree-table td{text-align:center;vertical-align:top;padding:8px 40px}

.node{display:flex;flex-direction:column;align-items:center;gap:6px}
.node img{width:42px;height:42px;border-radius:50%}
.node span{font-size:12px;font-weight:600;color:#333}

.node-red img{border:2px solid #e53935}
.node-yellow img{border:2px solid #fbc02d}
.node-green img{border:2px solid #43a047}
.node-blank{opacity:.35}

.graph{display:block;margin:auto;height:30px}
</style>
<div class="header">
    <h1>Team Tree</h1>
    <div class="user-info">👤 {{ Auth::user()->name }}</div>
</div>
<div class="card">
<div class="tree-scroll">

@php
/* ================= CORE HELPERS ================= */

function hasDescendantDeep($node){
    if(!$node) return false;
    if(!empty($node['username'])) return true;

    if(!empty($node['children'])){
        foreach($node['children'] as $child){
            if(hasDescendantDeep($child)) return true;
        }
    }
    return false;
}

function branchHasAnyUser($nodes){
    foreach($nodes as $n){
        if(hasDescendantDeep($n)) return true;
    }
    return false;
}

function getChildren($node){
    if(!$node || empty($node['children'])) return [null,null];
    return array_pad(array_slice($node['children'],0,2),2,null);
}

function nodeBox($node){
    if(!$node){
        return '<div class="node node-blank">
            <img src="'.asset('public/storage/r1.png').'">
            <span>Blank</span>
        </div>';
    }

    $color='node-red';
    if(!empty($node['children'])) $color='node-yellow';
    if(($node['investment_count'] ?? 0) >= 3) $color='node-green';

    return '<div class="node '.$color.'">
        <img src="'.($node['image'] ?? asset('public/storage/r1.png')).'">
        <span>'.$node['username'].'</span>
    </div>';
}
@endphp

@php
/* ================= PREPARE LEVELS ================= */

$l1 = $treeData;

$l2 = getChildren($l1);

$l3 = [];
foreach($l2 as $p){
    $l3[] = getChildren($p);
}

$l4 = [];
foreach($l3 as $pair){
    foreach($pair as $c){
        $l4[] = getChildren($c);
    }
}
@endphp

<table class="tree-table">

{{-- ================= LEVEL 1 ================= --}}
<tr>
    <td colspan="8">{!! nodeBox($l1) !!}</td>
</tr>

@if(branchHasAnyUser([$l1]))
<tr>
    <td colspan="8">
        <img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:360px">
    </td>
</tr>
@endif

{{-- ================= LEVEL 2 ================= --}}
@if(branchHasAnyUser($l2))
<tr>
    <td colspan="4">{!! nodeBox($l2[0]) !!}</td>
    <td colspan="4">{!! nodeBox($l2[1]) !!}</td>
</tr>

<tr>
    <td colspan="4"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:180px"></td>
    <td colspan="4"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:180px"></td>
</tr>
@endif

{{-- ================= LEVEL 3 ================= --}}
@if(branchHasAnyUser(array_merge(...$l3)))
<tr>
@foreach($l3 as $pair)
    <td colspan="2">{!! nodeBox($pair[0]) !!}</td>
    <td colspan="2">{!! nodeBox($pair[1]) !!}</td>
@endforeach
</tr>

<tr>
@for($i=0;$i<4;$i++)
    <td colspan="2"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:100px"></td>
@endfor
</tr>
@endif

{{-- ================= LEVEL 4 (3,4,5,6 FIXED HERE) ================= --}}
@if(branchHasAnyUser(array_merge(...$l4)))
<tr>
@foreach($l4 as $pair)
    <td>{!! nodeBox($pair[0]) !!}</td>
    <td>{!! nodeBox($pair[1]) !!}</td>
@endforeach
</tr>
@endif

</table>

</div>
</div>
@endsection
