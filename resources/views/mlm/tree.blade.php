  <h2 class="mb-4">My Binary Tree</h2>
  <ul class="tree">
    @include('mlm.partials.node',['user'=>$root])
  </ul>

  <style>
    .tree, .tree ul { list-style:none; }
    .tree ul { margin-left:1.5rem; }
    .node { padding:4px 8px; border:1px solid #ddd; display:inline-block; border-radius:6px; }
    .L { color:#2563eb; } .R { color:#16a34a; }
  </style>
