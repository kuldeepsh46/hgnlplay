@php
    /**
     * RENDERER: DESKTOP TABLE VIEW
     */
    if (!function_exists('renderDesktopTable')) {
        function renderDesktopTable($node) {
            if (!$node) return '';

            $getBox = function ($item) {
                if (!$item) {
                    return '
                    <div class="desktop-empty">
                        <img src="' . asset('assets/images/y1.png') . '"><br>
                        <span>EMPTY</span>
                    </div>';
                }

                $image = isset($item['is_active']) && $item['is_active'] ? asset('assets/images/g1.png') : asset('assets/images/r1.png');
                $url = route('team.tree', ['user_id' => $item['id']]);
                $pkg = number_format($item['personal_investment'] ?? 0, 2);

                return '
                <a href="' . $url . '" class="node-link">
                    <img src="' . $image . '"><br>
                    <span class="node-id">' . $item['member_id'] . '</span><br>
                    <strong class="node-user">' . $item['username'] . '</strong>
                    <span class="node-pkg">Pkg: ' . $pkg . '</span>
                </a>';
            };

            $l2l = $node['left'] ?? null; $l2r = $node['right'] ?? null;
            $l3ll = $l2l['left'] ?? null; $l3lr = $l2l['right'] ?? null;
            $l3rl = $l2r['left'] ?? null; $l3rr = $l2r['right'] ?? null;

            return '
            <div class="desktop-tree-only">
                <table class="tree-table">
                    <tbody>
                        <tr><td colspan="4">' . $getBox($node) . '</td></tr>
                        <tr>
                            <td colspan="2">' . $getBox($l2l) . '</td>
                            <td colspan="2">' . $getBox($l2r) . '</td>
                        </tr>
                        <tr>
                            <td>' . $getBox($l3ll) . '</td>
                            <td>' . $getBox($l3lr) . '</td>
                            <td>' . $getBox($l3rl) . '</td>
                            <td>' . $getBox($l3rr) . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>';
        }
    }

    /**
     * RENDERER: MOBILE NESTED VIEW
     */
    if (!function_exists('renderMobileNested')) {
        function renderMobileNested($item, $side = 'Root') {
            if (!$item) return '';

            $isActive = $item['is_active'] ?? false;
            $image = $isActive ? asset('assets/images/g1.png') : asset('assets/images/r1.png');
            $hasChildren = !empty($item['left']) || !empty($item['right']);
            
            $html = '<div class="mobile-node-group" x-data="{ open: false }">';
            $html .= '<div class="mobile-card" @click="open = !open">';
            
            if($side !== 'Root') {
                $sideClass = ($side == 'Left') ? 'side-l' : 'side-r';
                $html .= '<span class="side-badge '.$sideClass.'">'.$side.'</span>';
            }

            $html .= '<img src="'.$image.'">';
            $html .= '<div class="m-info">
                        <span class="m-id">'.$item['member_id'].'</span>
                        <span class="m-name">'.$item['username'].'</span>
                      </div>';
            
            if($hasChildren) {
                $html .= '<i class="fa fa-chevron-down toggle-icon" :class="open ? \'rotate\' : \'\'"></i>';
            }

            $html .= '</div>'; // End Card

            if($hasChildren) {
                $html .= '<div class="mobile-children" x-show="open" x-transition>';
                $html .= renderMobileNested($item['left'] ?? null, 'Left');
                $html .= renderMobileNested($item['right'] ?? null, 'Right');
                $html .= '</div>';
            }

            $html .= '</div>';
            return $html;
        }
    }
@endphp

<style>
    /* DESKTOP STYLES (Your existing preferred look) */
    .desktop-tree-only { width: 100%; min-width: 900px; background: #fff; padding: 20px; border-radius: 15px; }
    .tree-table { width: 100%; border-collapse: separate; table-layout: fixed; }
    .tree-table td { text-align: center; padding: 20px 0; border: 1px solid #f1f2f6; vertical-align: middle; }
    .node-link { text-decoration: none; display: block; }
    .node-link img { width: 52px; margin-bottom: 5px; }
    .node-id { color: #007bff; font-size: 11px; font-weight: 700; text-decoration: underline; }
    .node-user { font-size: 14px; color: #222; display: block; text-transform: uppercase; }
    .node-pkg { font-size: 12px; color: #555; font-weight: 600; display: block; }
    .desktop-empty { opacity: 0.2; padding: 20px 0; }
    .desktop-empty img { width: 40px; }

    /* MOBILE & TABLET STYLES */
    @media (max-width: 991px) {
        .desktop-tree-only { display: none; }
        .mobile-tree-only { display: block; width: 100%; }
        
        .mobile-node-group { margin-bottom: 12px; }
        .mobile-card { 
            background: #fff; border-radius: 12px; padding: 15px; 
            display: flex; align-items: center; gap: 15px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); cursor: pointer;
            position: relative; border: 1px solid #eee;
        }
        .mobile-card img { width: 45px; height: 45px; border-radius: 50%; }
        .m-info { display: flex; flex-direction: column; flex-grow: 1; }
        .m-id { font-size: 10px; color: #007bff; font-weight: 800; }
        .m-name { font-size: 15px; font-weight: 700; color: #333; }
        .toggle-icon { font-size: 14px; color: #ccc; transition: 0.3s; }
        .toggle-icon.rotate { transform: rotate(180deg); color: #007bff; }

        .mobile-children { 
            margin-left: 20px; padding-left: 15px; 
            border-left: 2px solid #007bff; margin-top: 10px; 
        }

        .side-badge { 
            position: absolute; top: -8px; left: 15px; 
            font-size: 9px; padding: 2px 8px; border-radius: 4px; font-weight: 900; 
        }
        .side-l { background: #00cec9; color: #000; }
        .side-r { background: #ff7675; color: #000; }
    }

    @media (min-width: 992px) {
        .mobile-tree-only { display: none; }
    }
</style>

{{-- TOP SUMMARY SECTION --}}
<div class="tree-summary-wrapper" style="display: flex; justify-content: space-between; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
    <div style="background: #2d3436; color: #fff; padding: 15px; border-radius: 10px; flex: 1; min-width: 200px; border-left: 5px solid #00cec9;">
        <small style="opacity:0.7">LEFT SIDE</small>
        <div style="font-weight: 800;">Biz: {{ number_format($node['total_business_left'] ?? 0, 2) }}</div>
    </div>
    
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; min-width: 200px;">
        <form action="{{ route('team.tree') }}" method="GET" style="width: 100%; max-width: 250px;">
            <input type="text" name="user_id" placeholder="Search ID..." value="{{ request('user_id') }}"
                   style="padding: 10px 20px; border-radius: 50px; border: 2px solid #dfe6e9; width: 100%; outline: none;">
        </form>
    </div>

    <div style="background: #2d3436; color: #fff; padding: 15px; border-radius: 10px; flex: 1; min-width: 200px; text-align: right; border-right: 5px solid #ff7675;">
        <small style="opacity:0.7">RIGHT SIDE</small>
        <div style="font-weight: 800;">Biz: {{ number_format($node['total_business_right'] ?? 0, 2) }}</div>
    </div>
</div>

{{-- ACTUAL TREE CONTENT --}}
<div class="tree-main-content">
    {!! renderDesktopTable($node) !!}

    <div class="mobile-tree-only">
        {!! renderMobileNested($node) !!}
    </div>
</div>