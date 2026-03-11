@extends('common.layout')
@section('title','Team Structure')
@section('main')
 
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 
@php
use App\Models\User;
 
/* ================= CONFIG ================= */
$MAX_LEVEL = 6;
$ROOT_ID = request()->segment(3) ?? auth()->id();
 
/* ================= SPONSOR TREE ================= */
function buildSponsorTree($userId, $level){
    if($level <= 0 || !$userId) return null;
 
    $user = User::find($userId);
    if(!$user) return null;
 
    $children = User::where('sponsor_id', $userId)->get();
 
    return [
        'id'    => $user->id,
        'name'  => $user->username ?? $user->email,
        'image' => $user->image
            ? asset('storage/'.$user->image)
            : asset('public/storage/r1.png'),
        'children' => $children->map(fn($c) =>
            buildSponsorTree($c->id, $level - 1)
        )->toArray(),
    ];
}
 
$tree = buildSponsorTree($ROOT_ID, $MAX_LEVEL);
 
/* ================= RENDER TREE ================= */
function renderTreeNode($node){
    if(!$node) return '';
 
    $html = '<li>';
 
    $html .= '
        <div class="node">
            <img src="'.$node['image'].'">
            <span>'.$node['name'].'</span>
        </div>
    ';
 
    if(!empty($node['children'])){
        $html .= '<ul>';
        foreach($node['children'] as $child){
            $html .= renderTreeNode($child);
        }
        $html .= '</ul>';
    }
 
    $html .= '</li>';
    return $html;
}
@endphp
 
<style>
/* ===== PAGE ===== */
body{
    background:#0b0f14;
    font-family:Segoe UI, Arial;
}
 
/* CARD */
.card{
    background:#fff;
    padding:50px 30px;
    border-radius:14px;
    overflow-x:auto;
}
 
/* TREE CONTAINER */
.tree{
    display:flex;
    justify-content:center;
    padding-bottom:40px;
}
 
/* ROOT LEVEL */
.tree > ul{
    position:relative;
    display:flex;
    justify-content:s;
    padding-top:40px;
}
 
/* ALL LEVELS */
.tree ul{
    position:relative;
    display:flex;
    justify-content:center;
    gap:80px;                /* ⭐ MAIN FIX */
    padding-top:40px;        /* vertical spacing */
}
 
/* VERTICAL CONNECTOR */
.tree ul::before{
    content:'';
    position:absolute;
    top:0;
    left:50%;
    /* height:40px; */
    border-left:2px solid #1e4f7a;
}
 
/* NODE WRAPPER */
.tree li{
    list-style:none;
    text-align:center;
    position:relative;
    min-width:120px;
}
 
/* HORIZONTAL CONNECTORS */
.tree li::before,
.tree li::after{
    content:'';
    position:absolute;
    top:0;
    width:50%;
    height:20px;
    border-top:2px solid #1e4f7a;
}
.tree li::before {
    right: 50%;
    border-right: 1px solid #000;
}
.tree li::after{ left:50%; }
 
/* CLEAN EDGES */
.tree li:only-child::before,
.tree li:only-child::after{ display:none; }
.tree li:first-child::before {
    border: none;
    border-right: 1px solid #000;
}
.tree li:last-child::after{ border:none; }
 
/* NODE UI */
.node{
    display:inline-flex;
    flex-direction:column;
    align-items:center;
    margin-top:10px;
    position:relative;
}
.node img{
    width:44px;
    height:44px;
}
.node span{
    margin-top:6px;
    font-size:12px;
    font-weight:600;
    color:#e53935;
    white-space:nowrap;
}
 
/* DOWN ARROW */
.node::before {
    content: "\f063";
    font-family: FontAwesome;
    position: absolute;
    bottom: -26px;
    color: #1e4f7a;
}
/* ROOT NO ARROW */
.tree > ul > li > .node::before{
    display:none;
}
 
/* MOBILE SAFETY */
@media(max-width:900px){
    .tree ul{
        gap:50px;
    }
.tree {
    display: flex;
    justify-content: start;
    padding-bottom: 40px;
}
 
}
.tree ul {
    position: relative;
    display: flex;
    justify-content: center;
    gap: 0px;
    padding-top: 54px;
    padding-left: 0px;
}
.treenode-ul > li:first-child > .node::after{
  content: "\f063";
    font-family: FontAwesome;
    position: absolute;
    bottom: -26px;
    color: #1e4f7a;
}
</style>
 
{{-- HEADER --}}
<div class="header">
    <h1>Team Structure</h1>
    <div class="user-info">
        👤 {{ Auth::user()->username ?? Auth::user()->email }}
    </div>
</div>
 
<div class="card">
    <div class="tree">
        <ul class="treenode-ul">
            {!! renderTreeNode($tree) !!}
        </ul>
    </div>
</div>
 
@endsection