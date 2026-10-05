{{-- Pairing card grid. Params: $title, $inputName, $options (Package::pairingOptions()), $selected (class keys) --}}
<div class="hgnl-field full-row" data-pair-group>
    <div class="hgnl-pair-head">
        <label>{{ $title }}</label>
        @if (count($options))
            <div class="hgnl-pair-tools">
                <span><span class="hgnl-pair-count" data-pair-count>0</span> selected</span>
                <button type="button" data-pair-all="1">Select all</button>
                <button type="button" data-pair-all="0">Clear</button>
            </div>
        @endif
    </div>
    @if (count($options))
        <div class="hgnl-pair-grid">
            @foreach ($options as $opt)
                <label class="hgnl-pair-card">
                    <input type="checkbox" name="{{ $inputName }}[]" value="{{ $opt['key'] }}"
                        @checked(in_array($opt['key'], $selected))>
                    <span class="hgnl-pair-tick">
                        <svg viewBox="0 0 16 16" fill="none" stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8.5l3.2 3L13 5" /></svg>
                    </span>
                    <span class="hgnl-pair-text">
                        <span class="hgnl-pair-name">{{ $opt['name'] }}</span>
                        <span class="hgnl-pair-amt">{{ $opt['note'] }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    @else
        <div class="hgnl-pair-empty">No other packages yet.</div>
    @endif
</div>
