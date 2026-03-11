<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $__env->yieldContent('title'); ?> - HGNL Pay</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --bg:#0b0e12;
    --card:#10171f;
    --sidebar:#0f141b;
    --accent:#3f7871;
    --text:#e9eef3;
    --muted:#a0acb3;
    --radius:12px;
      --bg: #0b0e12;
    --card: #10171f;
    --sidebar: #0f141b;
    --accent:#3f7871;
    --accent2: #3f7871;
    --border: #1b222b;
    --text: #e9eef3;
    --muted: #a0acb3;
    --radius: 12px;
 --bg:#0b0e12;
  --card:#10171f;
  --sidebar:#0f141b;

  --border:#1b222b;
  --text:#e9eef3;
  --muted:#a0acb3;
  --radius:12px;

}
div#passwordModal h2 {
    color: #ffff;
}
nav[aria-label="Pagination Navigation"] > div:first-child {

    padding-top: 20px;
}
/* ===== RESET ===== */
*{box-sizing:border-box}
.modal-content p {
    line-height: 1.6;
    text-align: center;
}
th, td {
 
    white-space: nowrap;
}
h2{
    margin-top:0px;
    font-size:22px;
}
body{
    margin:0;
    font-family:Inter,sans-serif;
    background:var(--bg);
    color:var(--text);
    min-height:100vh;
    display:flex;
}
.table-res {
    overflow-x: auto;
}
.toggle-btn {
    position: absolute;
    top: 16px;
    right: -18px;
    background: #3f7871 !important;
    color: #ffffffff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    cursor: pointer;
    box-shadow: 0 0 10px #a7ff1e66;
}
/* ===== SIDEBAR (from header file) ===== */
.sidebar{
    width:250px;
    background:var(--sidebar);
    border-right:1px solid #12181f;
    display:flex;
    flex-direction:column;
    position:relative;
    transition:.3s ease;
    z-index:1001;
}
.btn-primary{
    color: #ffffff !important;
}
.btn {
    
    color: #ffffff !important;
 
}
.user-info {
    color: #e84e6d !important;
}
.sidebar ul{list-style:none;margin:0;padding:0}
.sidebar ul li{
    padding:14px 18px;
    color:var(--muted);
    display:flex;
    align-items:center;
    gap:10px;
    border-left: 4px solid #a7ff1e00;
}
.sidebar ul li:hover,
.sidebar ul li.active{
    background:#141c26;
    color:#fff;
    border-left:4px solid var(--accent);
}

/* ===== MAIN CONTENT ===== */
main.main{
    flex:1;
    padding:20px;
    overflow-x:hidden;
}

/* ===== OVERLAY ===== */
#sidebarOverlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    display:none;
    z-index:1000;
}
.mobile-header {
    display: none;
}
/* ===== MOBILE FIXES ===== */
@media (max-width:768px){
    .profile-card{
        padding-bottom:20px !important;
    }
.toggle-btn {
  
    right: 19px;
  
   
}
    body{
        display:block !important;
    }

    .sidebar{
        position:fixed;
        top:20px;
        bottom:0;
        left:-260px;
        width:250px;
        transition:.3s ease;
    }

    .sidebar.open{
        left:0;
    }

    /* Overlay visible only when sidebar open */
    .sidebar.open + #sidebarOverlay{
        display:block;
    }

    /* Prevent background scroll */
    body.sidebar-open{
        overflow:hidden;
    }

    /* MAIN CONTENT FIX (IMPORTANT) */
    main.main{

        padding-top:110px; /* mobile header space */
    }
.mobile-header .logo img {
    height: 59px;
}
    /* Mobile header fix */
    .mobile-header{
        position:fixed;
        top:0;
        left:0;
        right:0;
        z-index:1100;
        background:var(--sidebar);
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:10px 13px;
        box-shadow:0 0 20px -10px;
    }
}

.logo img {
    width: 100%;
    height: 52px;
    object-fit: contain;
}
.logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 0px;
}

.logo h1 {
    font-size: 20px;
}

.logo h1 span {
    color: #e84e6d;
}
/* ===== DESKTOP ONLY ===== */
@media (min-width:769px){
    main.main{
        padding-left:50px;
    }
}

/* INPUT FIX */
input[type="date"]::-webkit-calendar-picker-indicator{
    filter:invert(1);
    cursor:pointer;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.header h1{
    margin:0px;    font-size: 22px;
}

.user-info {
    background: #141c22;
    padding: 8px 14px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--accent);
    font-weight: 600;
}


/* Team Accordion */
.team-item {
    list-style: none;
}

/* Header */
.team-menu {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    cursor: pointer;
    color: var(--muted);
    user-select: none;
}

.team-item.active > .team-menu {
    color: #fff;
}

/* Arrow */
.team-menu .arrow {
    transition: transform 0.3s ease;
    font-size: 14px;
}

/* Rotate when open */
.team-item.open .arrow {
    transform: rotate(180deg);
}

/* Submenu */
.submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease;
    padding-left: 18px;
}
ul.submenu li {
    border-left: unset;
    padding: 14px 18px !important;
}
ul.submenu li:hover {
    border-left: unset;
    padding: 0px;
}
/* Open state */
.team-item.open .submenu {
    max-height: 500px; /* enough for items */
}

/* Submenu links */
.submenu li a {
    display: block;
    padding: 10px 12px;
    color: var(--muted);
    text-decoration: none;
    border-left: 3px solid transparent;
}

.submenu li a:hover,
.submenu li a.active {
    color: #fff;
    background: #141c26;
    border-left: 3px solid var(--accent);
}
.btn-view {
    background: #a7ff1e;
    color: #000;
    border: none;
    padding: 10px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>


<?php echo $__env->make('common.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div id="sidebarOverlay"></div>


<main class="main">
    <?php echo $__env->yieldContent('main'); ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('toggleBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const main    = document.querySelector('main');

    function openSidebar(){
        sidebar.classList.add('open');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar(){
        sidebar.classList.remove('open');
        document.body.classList.remove('sidebar-open');
    }

    /* Toggle */
    if(toggle){
        toggle.addEventListener('click', function(){
            if(window.innerWidth <= 768){
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            }
        });
    }

    /* Click on overlay */
    overlay.addEventListener('click', closeSidebar);

    /* Click on main content */
    main.addEventListener('click', function(){
        if(window.innerWidth <= 768 && sidebar.classList.contains('open')){
            closeSidebar();
        }
    });

    /* Sidebar link click */
    document.querySelectorAll('.sidebar a').forEach(link=>{
        link.addEventListener('click', function(){
            if(window.innerWidth <= 768){
                closeSidebar();
            }
        });
    });

    /* ESC key */
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            closeSidebar();
        }
    });
});
</script>
<script>
document.querySelectorAll('.team-menu').forEach(menu => {
    menu.addEventListener('click', () => {
        const parent = menu.closest('.team-item');

        // Close others (optional – accordion behavior)
        document.querySelectorAll('.team-item').forEach(item => {
            if (item !== parent) item.classList.remove('open');
        });

        parent.classList.toggle('open');
    });
});
</script>

</body>
</html>
<?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/common/layout.blade.php ENDPATH**/ ?>