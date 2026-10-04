<style>
.atx .card { background: var(--card); border: 1px solid #1f2832; border-radius: var(--radius); padding: 20px; margin-bottom: 20px; }
.atx h2 { margin: 0 0 12px; font-size: 17px; color: var(--accent); }
.atx .muted { color: #8899a8; font-size: 13px; margin: 0 0 12px; }
.atx .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.atx .tab { padding: 9px 16px; border-radius: 8px; background: #333; color: #fff; text-decoration: none; font-weight: 600; font-size: 14px; }
.atx .tab.on { background: var(--accent); }
.atx .filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; align-items: end; }
.atx .filters label { display: block; font-size: 12px; margin-bottom: 5px; color: #a9b9c7; }
.atx .filters input, .atx .filters select { width: 100%; box-sizing: border-box; padding: 8px; border-radius: 6px; background: #141c22; border: 1px solid #1f2832; color: #fff; }
.atx details.types { grid-column: 1 / -1; background: #141c22; border: 1px solid #1f2832; border-radius: 8px; padding: 8px 12px; }
.atx details.types summary { cursor: pointer; font-size: 13px; color: #a9b9c7; }
.atx .type-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 6px; margin-top: 10px; }
.atx .type-grid label { display: flex; gap: 6px; align-items: center; font-size: 13px; color: #d4dee8; margin: 0; }
.atx .type-grid input { width: auto; }
.atx .actions { display: flex; gap: 8px; flex-wrap: wrap; grid-column: 1 / -1; }
.atx .btn { padding: 9px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; font-size: 14px; }
.atx .btn-primary { background: var(--accent); color: #fff; }
.atx .btn-dark { background: #333; color: #fff; }
.atx .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin: 16px 0; }
.atx .stat { background: #141c22; border: 1px solid #1f2832; border-radius: 10px; padding: 14px; }
.atx .stat p { margin: 0; font-size: 12px; color: #a9b9c7; }
.atx .stat h3 { margin: 6px 0 0; font-size: 18px; }
.atx .table-wrap { overflow-x: auto; }
.atx table { width: 100%; border-collapse: collapse; margin-top: 8px; }
.atx th, .atx td { border: 1px solid #1e2b36; padding: 9px; font-size: 13px; text-align: left; vertical-align: top; }
.atx th { background: #161f29; color: #a9b9c7; white-space: nowrap; }
.atx td { color: #d4dee8; }
.atx td.num, .atx th.num { text-align: right; white-space: nowrap; }
.atx td small { display: block; color: #8899a8; }
.atx .nowrap { white-space: nowrap; }
.atx .remarks { min-width: 260px; max-width: 520px; }
.atx .pill { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; white-space: nowrap; }
.atx .pill.credit, .atx .pill.completed, .atx .pill.approved { background: #1d3b2f; color: #5fd39a; }
.atx .pill.debit, .atx .pill.rejected { background: #3d1f22; color: #ff8a8a; }
.atx .pill.pending { background: #3a321a; color: #f2c94c; }
.atx .pill.type { background: #1d2a3a; color: #8cc4ff; }
.atx a.link { color: var(--accent); text-decoration: none; font-weight: 600; }
.atx dl.kv { display: grid; grid-template-columns: 170px 1fr; gap: 8px 14px; margin: 0; font-size: 14px; }
.atx dl.kv dt { color: #8899a8; }
.atx dl.kv dd { margin: 0; color: #d4dee8; word-break: break-word; }
.atx .grid2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
.atx .how { background: #141c22; border-left: 3px solid var(--accent); padding: 12px 14px; border-radius: 6px; color: #d4dee8; font-size: 14px; }
@media (max-width: 600px) { .atx dl.kv { grid-template-columns: 1fr; } }
</style>
