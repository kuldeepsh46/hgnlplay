@extends('common.layout')
@section('title', 'Team Structure')
@section('main')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            background: #0b0f14;
            font-family: Segoe UI, Arial;
            margin: 0;
        }

        /* CARD */
        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            overflow-x: auto;
        }

        /* ======================
                               TREE BASE
                            ====================== */
        .tree {
            display: flex;
            justify-content: center;
        }

        /* ROOT UL */
        .tree ul {
            padding-top: 40px;
            position: relative;
            display: flex;
            justify-content: center;
            padding-left: 0;
        }

        /* VERTICAL LINE (DOWN) */
        .tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #0b3c5d;
        }

        .node {
            position: relative;
        }

        /* LI */
        .tree li {
            list-style: none;
            text-align: center;
            position: relative;
            padding: 2px 21px 0 24px;
        }

        /* HORIZONTAL CONNECTORS */
        .tree li::before,
        .tree li::after {
            content: '';
            position: absolute;
            top: 0;
            width: 50%;
            height: 20px;
            border-top: 3px solid #0b3c5d;
        }

        .tree li::before {
            left: 0;
            border-right: 3px solid #0b3c5d;
        }

        .tree li::after {
            right: 0;
            border-left: 3px solid #0b3c5d;
        }

        /* ARROW (TOP DEFAULT) */
        .node::before {
            content: "\f063";
            font-family: FontAwesome;
            position: absolute;
            top: -28px;
            left: -6.1px;
            font-size: 16px;
            color: #0b3c5d;
            right: 2px;
            margin: auto;
        }

        .treenode-ul {
            position: relative;
        }

        .fisrt-level-node {
            position: relative;
        }

        /* REMOVE EXTRA LINES */
        .tree li:only-child::before,
        .tree li:only-child::after {
            display: none;
        }

        .tree li:first-child::before {
            border: none;
        }

        .tree li:last-child::after {
            border: none;
        }

        /* NODE BOX */
        .node {
            display: inline-block;
            padding: 6px;
        }

        .node img {
            width: 36px;
            height: 42px;
        }

        .node span {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-top: 4px;
            color: red;
        }

        /* NO CHILD LINE */
        .tree li.leaf>ul::before {
            display: none;
        }

        /* FIRST ROOT ARROW CHANGE */
        .tree>ul.treenode-ul>li:first-child>.node::before {
            display: none;
        }

        .tree>ul.treenode-ul>li:first-child>.node::after {
            content: "\f063";
            font-family: FontAwesome;
            position: absolute;
            bottom: -22px;
            left: 50%;
            transform: translateX(-50%);
            color: red;
        }

        /* =====================================================
                               ✅ ONLY NEW CSS (EMPTY LEAF USER – NOTHING ELSE TOUCHED)
                            ===================================================== */
        .tree li.leaf .node span:empty {
            color: #999;
            /* different look */
            font-style: italic;
            font-size: 11px;
        }

        /* span blank hi rahega */
        .tree li.leaf .node span:empty::before {
            content: "";
        }

        .node.empty-node {
            margin-top: 43px;
        }

        .node.empty-node::before {
            content: "\f063";
            font-family: FontAwesome;
            position: absolute;
            top: -28px;
            left: -4px;
            font-size: 16px;
            color: #0b3c5d;
            right: 0;
            margin: auto;
        }

        .node.empty-node p {
            padding: 0px;
            margin: 0px;
            visibility: hidden;
        }

        @media (max-width:660px) {
            .card {
                padding: 0px;
            }

            .tree {
                display: flex;
                /*justify-content: start;*/
            }

            .tree ul {
                justify-content: start;
            }

            .tree li {
                list-style: none;
                text-align: center;
                position: relative;
                padding: 2px 5px 0 2px;
            }

            .node::before {
                content: "\f063";
                font-family: FontAwesome;
                position: absolute;
                top: -28px;
                left: 4.9px;
                font-size: 16px;
                color: #0b3c5d;
                right: 0;
                margin: auto;
            }

            .node.empty-node::before {
                content: "\f063";
                font-family: FontAwesome;
                position: absolute;
                top: -28px;
                left: 4px;
                font-size: 16px;
                color: #0b3c5d;
                right: 0;
                margin: auto;
            }

            .node.empty-node.r:before {
                content: "\f063";
                font-family: FontAwesome;
                position: absolute;
                top: -28px;
                left: 7px !important;
                font-size: 16px;
                color: #0b3c5d;
                right: 0;
                margin: auto;
            }

            .node.empty-node.l:before {
                content: "\f063";
                font-family: FontAwesome;
                position: absolute;
                top: -28px;
                left: 0px !important;
                font-size: 16px;
                color: #0b3c5d;
                right: 0 !important;
                margin: auto;
            }

            ul.treenode-ul {
                max-width: 100%;
            }

            ul {}

            ul.treenode-ul {
                justify-content: flex-start !important;
            }

            .tree ul {
                justify-content: flex-start !important;
            }

            .tree {
                justify-content: flex-start !important;
            }
        }

        .node.empty-node.l:before {
            content: "\f063";
            font-family: FontAwesome;
            position: absolute;
            top: -28px;
            left: -6px;
            font-size: 16px;
            color: #0b3c5d;
            right: 0;
            margin: auto;
        }

        .node.empty-node.r:before {
            content: "\f063";
            font-family: FontAwesome;
            position: absolute;
            top: -28px;
            left: 0px;
            font-size: 16px;
            color: #0b3c5d;
            right: 0;
            margin: auto;
        }

        span.text-success {
            color: #0c860c;
        }

        ul.treenode-ul {
            max-width: 700px;
        }

        .tree {
            overflow: auto;
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
        <div class="tree">
            {!! renderTreeUL($node) !!}
        </div>
    </div>

@endsection


@php
    /* ======================
   RECURSIVE UL-LI TREE
====================== */
    // function renderTreeUL($node, $currentLevel = 1, $maxLevel = 3)
    // {
    //     if (!$node || $currentLevel > $maxLevel) {
    //         return '';
    //     }
    //     if (!$node) {
    //         return '';
    //     }

    //     $getTr = DB::table('transactions')->where('user_id', $node['id'])->get();

    //     $totalEmi = count($getTr);

    //     $emiClass = $node['investment_count'] > 0 ? 'text-success' : '';
    //     // IMAGE BASED ON INVESTMENT
    //     $image =
    //         $node['investment_count'] > 0
    //             ? asset('/storage/g1.png') // GREEN
    //             : asset('public/storage/r1.png'); // RED

    //     $hasChild = !empty($node['left']) || !empty($node['right']);

    //     $html = '<ul class="treenode-ul">';
    //     $html .= '<li class="' . (!$hasChild ? 'leaf' : '') . '">';
    //     $url = route('team.tree', ['user_id' => $node['id']]);
    //     // NODE
    //     $html .=
    //         '
// <a href="' .
    //         $url .
    //         '">
//     <div class="node fisrt-level-node">
//         <img src="' .
    //         $image .
    //         '">
//         <span class="' .
    //         $emiClass .
    //         '">
//             ' .
    //         $node['username'] .
    //         '
//         </span>
//     </div>
//     </a>
// ';

    //     // CHILDREN
    //     // CHILDREN (LIMITED TO LEVEL 3)
    //     if ($hasChild && $currentLevel < $maxLevel) {
    //         $html .= '<ul>';

    //         if (!empty($node['left'])) {
    //             $html .= '<li>' . renderTreeUL($node['left'], $currentLevel + 1, $maxLevel) . '</li>';
    //         } else {
    //             $html .=
    //                 '<li class="leaf">
//         <div class="node empty-node l">
//             <img src="' .
    //                 asset('public/storage/y1.png') .
    //                 '">
//             <span>BLANK</span>
//         </div>
//     </li>';
    //         }

    //         if (!empty($node['right'])) {
    //             $html .= '<li>' . renderTreeUL($node['right'], $currentLevel + 1, $maxLevel) . '</li>';
    //         } else {
    //             $html .=
    //                 '<li class="leaf">
//         <div class="node empty-node r">
//             <img src="' .
    //                 asset('public/storage/y1.png') .
    //                 '">
//             <span>BLANK</span>
//         </div>
//     </li>';
    //         }

    //         $html .= '</ul>';
    //     }

    //     $html .= '</li>';
    //     $html .= '</ul>';

    //     return $html;
    // }
    // function renderTreeUL($node, $currentLevel = 1, $maxLevel = 3)
    // {
    //     if (!$node) return '';

    //     // 1. Box Helper (Forces horizontal alignment)
    //     $getBox = function($item) {
    //         if (!$item) {
    //             return '
//                 <div style="padding: 20px 0; opacity: 0.3;">
//                     <img src="'.asset('storage/y1.png').'" style="width:40px;"><br>
//                     <span style="font-size: 10px; color: #999;">EMPTY</span>
//                 </div>';
    //         }

    //         $image = $item['investment_count'] > 0 ? asset('storage/g1.png') : asset('storage/r1.png');
    //         $url = route('team.tree', ['user_id' => $item['id']]);

    //         return '
//             <a href="'.$url.'" style="text-decoration:none; color:inherit; display: block; padding: 10px 0;">
//                 <img src="'.$image.'" style="width:50px; margin-bottom: 5px;"><br>
//                 <span style="color: #007bff; font-size: 11px; text-decoration: underline;">'.$item['id'].'</span><br>
//                 <strong style="font-size: 14px; display: block; text-transform: uppercase;">'.$item['username'].'</strong>
//                 <span style="font-size: 11px; color: #666;">Package : 1000.00</span>
//             </a>';
    //     };

    //     $l2l = $node['left'] ?? null; $l2r = $node['right'] ?? null;
    //     $l3ll = $l2l['left'] ?? null; $l3lr = $l2l['right'] ?? null;
    //     $l3rl = $l2r['left'] ?? null; $l3rr = $l2r['right'] ?? null;

    //     // 2. The Layout
    //     $html = '
//     <div style="width: 100%; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow-x: auto;">

//         <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
//             <div style="background: #444; color: #fff; padding: 15px; border-radius: 8px; min-width: 200px; flex: 1;">
//                 <div style="font-size: 12px;">Left IDs : 2</div>
//                 <div style="font-size: 12px;">Left Business : 2000.00</div>
//                 <div style="font-size: 12px; font-weight: bold;">Left New Business : 2000.00</div>
//             </div>

//             <div style="flex: 1; text-align: center;">
//                 <input type="text" placeholder="ID Search" style="padding: 8px 20px; border-radius: 20px; border: 1px solid #ddd; width: 100%; max-width: 180px;">
//             </div>

//             <div style="background: #444; color: #fff; padding: 15px; border-radius: 8px; min-width: 200px; flex: 1; text-align: right;">
//                 <div style="font-size: 12px;">Right IDs : 2</div>
//                 <div style="font-size: 12px;">Right Business : 1000.00</div>
//                 <div style="font-size: 12px; font-weight: bold;">Right New Business : 1000.00</div>
//             </div>
//         </div>

//         <table style="width: 100%; min-width: 800px; border-collapse: collapse; table-layout: fixed; border: 1px solid #f0f0f0;">
//             <tbody>
//                 <tr>
//                     <td colspan="4" style="text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">
//                         '.$getBox($node).'
//                     </td>
//                 </tr>
//                 <tr>
//                     <td colspan="2" style="width: 50%; text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">
//                         '.$getBox($l2l).'
//                     </td>
//                     <td colspan="2" style="width: 50%; text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">
//                         '.$getBox($l2r).'
//                     </td>
//                 </tr>
//                 <tr>
//                     <td style="width: 25%; text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">'.$getBox($l3ll).'</td>
//                     <td style="width: 25%; text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">'.$getBox($l3lr).'</td>
//                     <td style="width: 25%; text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">'.$getBox($l3rl).'</td>
//                     <td style="width: 25%; text-align: center; border: 1px solid #f0f0f0; vertical-align: middle;">'.$getBox($l3rr).'</td>
//                 </tr>
//             </tbody>
//         </table>
//     </div>';

    //     return $html;
    // }
    function renderTreeUL($node, $currentLevel = 1, $maxLevel = 3)
    {
        // Safety check if node is empty
        if (!$node) {
            return '<div class="text-center p-5">No tree data found.</div>';
        }

        /**
         * Helper: getBox
         * Renders an individual user node (Image, ID, Name, Package)
         */
        $getBox = function ($item) {
            if (!$item) {
                return '
            <div style="padding: 20px 0; opacity: 0.2;">
                <img src="' .
                    asset('assets/images/y1.png') .
                    '" style="width:40px;"><br>
                <span style="font-size: 10px; color: #999; font-weight: bold;">EMPTY</span>
            </div>';
            }

            // Logic: Green (g1) if is_active is true, otherwise Red (r1) for pending/zero investment
            $image =
                isset($item['is_active']) && $item['is_active'] ? asset('assets/images/g1.png') : asset('assets/images/r1.png');

            $url = route('team.tree', ['user_id' => $item['id']]);
            $pkgAmount = number_format($item['personal_investment'] ?? 0, 2);

            return '
        <a href="' .
                $url .
                '" style="text-decoration:none; color:inherit; display: block; padding: 10px 0;">
            <img src="' .
                $image .
                '" style="width:52px; margin-bottom: 5px; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.1));"><br>
            <span style="color: #007bff; font-size: 11px; font-weight: 700; text-decoration: underline;">' .
                $item['member_id'] .
                '</span><br>
            <strong style="font-size: 14px; color: #222; display: block; text-transform: uppercase; margin-top: 3px; letter-spacing: 0.5px;">' .
                $item['username'] .
                '</strong>
            <span style="font-size: 12px; color: #555; font-weight: 600; display: block; margin-top: 2px;">Package: ' .
                $pkgAmount .
                '</span>
        </a>';
        };

        // Mapping levels based on your array structure
        $l2l = $node['left'] ?? null;
        $l2r = $node['right'] ?? null;
        $l3ll = $l2l['left'] ?? null;
        $l3lr = $l2l['right'] ?? null;
        $l3rl = $l2r['left'] ?? null;
        $l3rr = $l2r['right'] ?? null;

        $html =
            '
    <div style="width: 100%; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow-x: auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
            
            <div style="background: #2d3436; color: #ffffff; padding: 20px; border-radius: 12px; min-width: 240px; flex: 1; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border-left: 5px solid #00cec9;">
                <div style="font-size: 12px; text-transform: uppercase; opacity: 0.7; margin-bottom: 5px;">Left Side Summary</div>
                <div style="font-size: 14px; margin-bottom: 4px;">Contributors: <span style="font-weight:bold; color: #00cec9;">' .
            ($node['total_contributors_left'] ?? 0) .
            '</span></div>
                <div style="font-size: 14px;">Total Business: <span style="font-weight:bold; color: #00cec9;">' .
            number_format($node['total_business_left'] ?? 0, 2) .
            '</span></div>
            </div>
            
            

    
<div style="flex: 1; display: flex; align-items: center; justify-content: center; min-width: 200px;">
    <form id="searchForm" action="' .
            route('team.tree') .
            '" method="GET" style="width: 100%; max-width: 250px;">
        <input type="text" 
               name="user_id" 
               id="searchInput"
               placeholder="Search User ID..." 
               style="padding: 12px 20px; border-radius: 50px; border: 2px solid #dfe6e9; width: 100%; font-size: 14px; outline: none; transition: all 0.3s;"
               value="' .
            request('user_id') .
            '"
               oninput="if(this.value.length >= 3) { document.getElementById(\'searchForm\').submit(); }"
               autocomplete="off">
    </form>
</div>
            <div style="background: #2d3436; color: #ffffff; padding: 20px; border-radius: 12px; min-width: 240px; flex: 1; text-align: right; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border-right: 5px solid #ff7675;">
                <div style="font-size: 12px; text-transform: uppercase; opacity: 0.7; margin-bottom: 5px;">Right Side Summary</div>
                <div style="font-size: 14px; margin-bottom: 4px;">Contributors: <span style="font-weight:bold; color: #ff7675;">' .
            ($node['total_contributors_right'] ?? 0) .
            '</span></div>
                <div style="font-size: 14px;">Total Business: <span style="font-weight:bold; color: #ff7675;">' .
            number_format($node['total_business_right'] ?? 0, 2) .
            '</span></div>
            </div>
        </div>

        <div style="min-width: 900px;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; table-layout: fixed;">
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px 0; border: 1px solid #f1f2f6; background: #f9f9f9; border-radius: 10px 10px 0 0;">
                            ' .
            $getBox($node) .
            '
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%; text-align: center; border: 1px solid #f1f2f6; padding: 20px 0;">
                            ' .
            $getBox($l2l) .
            '
                        </td>
                        <td colspan="2" style="width: 50%; text-align: center; border: 1px solid #f1f2f6; padding: 20px 0;">
                            ' .
            $getBox($l2r) .
            '
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; text-align: center; border: 1px solid #f1f2f6; padding: 15px 0;">' .
            $getBox($l3ll) .
            '</td>
                        <td style="width: 25%; text-align: center; border: 1px solid #f1f2f6; padding: 15px 0;">' .
            $getBox($l3lr) .
            '</td>
                        <td style="width: 25%; text-align: center; border: 1px solid #f1f2f6; padding: 15px 0;">' .
            $getBox($l3rl) .
            '</td>
                        <td style="width: 25%; text-align: center; border: 1px solid #f1f2f6; padding: 15px 0;">' .
            $getBox($l3rr) .
            '</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>';

        return $html;
    }
@endphp