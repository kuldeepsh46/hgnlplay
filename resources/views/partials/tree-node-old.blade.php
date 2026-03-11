@extends('common.layout')
@section('title', 'Team Structure')
@section('main')

<style>
body{
    background:radial-gradient(circle at top,#121820,#05070a);
    font-family:Segoe UI,Arial;
}
.card{
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 0 20px rgba(0,0,0,.4); 
}
.mlm-wrap{
    width:100%;
    overflow-x:auto;
}
.mlm-table{
    border-collapse:collapse;
    margin:auto;
}
.mlm-table td{
    text-align:center;
    vertical-align:top;
    padding:5px 26px;
}
.user-img{
    width:35px;
    height:40px;
}
.user-name{
    display:block;
    font-size:12px;
    font-weight:600;
}
.red{color:red}
.green{color:green}
.black{color:#999}
.graph{
    height:30px;
}
</style>

{{-- ================= HEADER ================= --}}
<div class="header">
    <h1>Team Structure</h1>
    <div class="user-info">
        👤 {{ Auth::user()->username ?? Auth::user()->email }}
    </div>
</div>

<div class="card">
<div class="mlm-wrap">

@php
/* ================= HELPERS ================= */

function userBox($img,$name,$color){
    return "
        <img src='{$img}' class='user-img'><br>
        <span class='user-name {$color}'>".($name ?: '')."</span>
    ";
}

function uname($node){
    return $node['username'] ?? '';
}
@endphp

<table class="mlm-table">

{{-- ================= LEVEL 1 ================= --}}
<tr>
    <td colspan="8">
        {!! userBox(asset('public/storage/r1.png'), uname($node), 'red') !!}
    </td>
</tr>

<tr>
    <td colspan="8">
        <img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:360px">
    </td>
</tr>

{{-- ================= LEVEL 2 ================= --}}
<tr>
    <td colspan="4">
        {!! userBox(asset('/storage/g1.png'), uname($node['left'] ?? null), 'green') !!}
    </td>
    <td colspan="4">
        {!! userBox(asset('/storage/r1.png'), uname($node['right'] ?? null), 'red') !!}
    </td>
</tr>

<tr>
    <td colspan="4">
        <img src="{{ asset('/storage/garph01.png') }}" class="graph" style="width:180px">
    </td>
    <td colspan="4">
        <img src="{{ asset('/storage/garph01.png') }}" class="graph" style="width:180px">
    </td>
</tr>

{{-- ================= LEVEL 3 ================= --}}
<tr>
    <td colspan="2">
        {!! userBox(asset('public/storage/r1.png'), uname($node['left']['left'] ?? null), 'red') !!}
    </td>
    <td colspan="2">
        {!! userBox(asset('/storage/g1.png'), uname($node['left']['right'] ?? null), 'green') !!}
    </td>
    <td colspan="2">
        {!! userBox(asset('/storage/r1.png'), uname($node['right']['left'] ?? null), 'red') !!}
    </td>
    <td colspan="2">
        {!! userBox(asset('/storage/g1.png'), uname($node['right']['right'] ?? null), 'green') !!}
    </td>
</tr>

<tr>
    <td colspan="2"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:100px"></td>
    <td colspan="2"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:100px"></td>
    <td colspan="2"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:100px"></td>
    <td colspan="2"><img src="{{ asset('public/storage/garph01.png') }}" class="graph" style="width:100px"></td>
</tr>

{{-- ================= LEVEL 4 ================= --}}
@php
$level4 = [
    $node['left']['left']['left'] ?? null,
    $node['left']['left']['right'] ?? null,
    $node['left']['right']['left'] ?? null,
    $node['left']['right']['right'] ?? null,
    $node['right']['left']['left'] ?? null,
    $node['right']['left']['right'] ?? null,
    $node['right']['right']['left'] ?? null,
    $node['right']['right']['right'] ?? null,
];
@endphp

<tr>
@foreach($level4 as $n)
<td>
    {!! userBox(
        asset('public/storage/y1.png'),
        uname($n),
        $n ? 'green' : 'black'
    ) !!}
</td>
@endforeach
</tr>

</table>

</div>
</div>

@endsection
