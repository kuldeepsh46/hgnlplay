<style>
 /* =========================
   TREE WRAPPER
========================= */
.tree {
    text-align: center;
    position: relative;
    padding-top: 20px;
    width: 100%;
}

/* =========================
   CHILD CONTAINER
========================= */
.tree ul {
    padding-top: 24px;
    position: relative;
    display: flex;
    justify-content: center;
    gap: 24px; /* controls horizontal spacing */
    margin: 0;
}

/* Horizontal connector */
.tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 70%;               /* 👈 shrink width */
    height: 2px;
    background: #666;
    transform: translateX(-50%);
}

/* =========================
   TREE NODE CONTAINER
========================= */
.tree ul li {
    list-style: none;
    position: relative;
    padding-top: 20px;
    min-width: 90px;
}

/* Vertical connectors */
.tree ul li::before,
.tree ul li::after {
    content: '';
    position: absolute;
    top: 0;
    width: 2px;
    height: 20px;
    background: #666;
}

.tree ul li::before { left: 50%; }
.tree ul li::after  { right: 50%; }

/* Remove unnecessary lines */
.tree ul li:only-child::before,
.tree ul li:only-child::after {
    display: none;
}

.tree ul li:first-child::before,
.tree ul li:last-child::after {
    background: none;
}

/* =========================
   NODE DESIGN
========================= */
.node-img {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    transform: scale(1);
    transition: transform 0.2s ease;
}

.node-img:hover {
    transform: scale(1.05);
}

.node-img img {
    width: 38px;
    height: auto;
}

.node-text {
    font-size: 11px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
}

/* Blank nodes (spacing only) */
.node-img.blank {
    visibility: hidden;
}

/* =========================
   RESPONSIVE BREAKPOINTS
========================= */

/* Tablets */
@media (max-width: 1024px) {
    .tree ul {
        gap: 16px;
    }

    .node-img img {
        width: 34px;
    }

    .node-text {
        font-size: 10px;
    }
}

/* Mobile */
@media (max-width: 768px) {
    .tree {
        /* transform: scale(0.9); */
        transform-origin: top center;
    }

    .tree ul {
        gap: 12px;
    }

    .node-img img {
        width: 30px;
    }

    .node-text {
        font-size: 9px;
    }
}

/* Very small screens */
@media (max-width: 480px) {
    .tree {
        /* transform: scale(0.8); */
    }

    .tree ul::before {
        width: 60%;
    }
}


</style>

@php
    $statusImages = [
        'red'    => 'https://mlm.hgnlhimparvat.com/storage/r1.png',
        'green'  => 'https://mlm.hgnlhimparvat.com/storage/g1.png',
        'black'  => 'https://mlm.hgnlhimparvat.com/storage/y1.png',
        'orange' => 'https://mlm.hgnlhimparvat.com/storage/yl1.png',
    ];

    $img = $statusImages[$node['color']] ?? $statusImages['black'];
@endphp



@if($node)
<div class="tree">

    <!-- NODE -->
    <div class="node-img">
        <img src="{{ $img }}" alt="{{ $node['username'] }}">
        <span class="node-text">{{ $node['username'] }}</span>
    </div>

    @if($node['left'] || $node['right'])
        <ul>
            <li>
                @if($node['left'])
                    @include('partials.tree-node', ['node' => $node['left']])
                @else
                    <div class="node-img blank">
                        <span>Blank</span>
                    </div>
                @endif
            </li>

            <li>
                @if($node['right'])
                    @include('partials.tree-node', ['node' => $node['right']])
                @else
                    <div class="node-img blank">
                        <span>Blank</span>
                    </div>
                @endif
            </li>
        </ul>
    @endif

</div>
@endif
