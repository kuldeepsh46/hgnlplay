<li>
  <span class="node">
    {{ $user->username }} ({{ $user->name }})
    @if($user->node) <span class="{{ $user->node->side }}">{{ $user->node->side }}</span> @endif
  </span>
  @php
    $left  = optional(\App\Models\BinaryNode::where('parent_id',$user->id)->where('side','L')->first())->user ?? null;
    $right = optional(\App\Models\BinaryNode::where('parent_id',$user->id)->where('side','R')->first())->user ?? null;
  @endphp
  @if($left || $right)
    <ul>
      <li>
        @if($left)
          @include('mlm.partials.node',['user'=>$left])
        @else
          <em>Empty (L)</em>
        @endif
      </li>
      <li>
        @if($right)
          @include('mlm.partials.node',['user'=>$right])
        @else
          <em>Empty (R)</em>
        @endif
      </li>
    </ul>
  @endif
</li>
