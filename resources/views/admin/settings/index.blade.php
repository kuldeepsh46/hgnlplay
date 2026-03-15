@extends('common.layout')
@section('title', 'Settings')
<style>
    /* Main Container Styling */
    .qr-main-container {
        background: linear-gradient(145deg, #161f29, #0f1620);
        border: 1px solid #1f2832;
        border-radius: 20px;
        padding: 60px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.4);
    }

    /* The Image Wrapper */
    .qr-wrapper {
        position: relative;
        background: #ffffff;
        padding: 12px;
        border-radius: 16px;
        cursor: pointer;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 4px solid transparent;
    }

    .qr-wrapper:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px rgba(0, 200, 150, 0.2);
        border-color: var(--accent);
    }

    .qr-wrapper img {
        width: 220px;
        height: 220px;
        display: block;
        object-fit: contain;
        border-radius: 8px;
    }

    /* Hover Overlay Effect */
    .qr-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 200, 150, 0.8);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: #000;
        font-weight: 800;
        z-index: 2;
    }

    .qr-wrapper:hover .qr-overlay {
        opacity: 1;
    }

    .qr-overlay i {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .enlarge-text {
        margin-top: 20px;
        color: var(--muted);
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    /* Modal Styling */
    .qr-modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        backdrop-filter: blur(10px);
        cursor: zoom-out;
    }

    .modal-content-wrapper {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    #qr-large-display {
        max-width: 90vw;
        max-height: 80vh;
        border-radius: 20px;
        border: 5px solid #fff;
        box-shadow: 0 0 50px rgba(0, 200, 150, 0.3);
        animation: popOut 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes popOut {
        from {
            transform: scale(0.8);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-close {
        position: absolute;
        top: 30px;
        right: 40px;
        color: #fff;
        font-size: 50px;
        cursor: pointer;
        line-height: 1;
    }

    .modal-caption {
        color: #fff;
        margin-top: 20px;
        font-size: 18px;
        font-weight: 600;
    }
</style>
@section('main')
    {{-- Removed the nested @section('content') --}}
    <div class="settings-wrapper" style="width: 100%; padding: 20px; min-height: 100vh;">

        <div class="header">
            <h2 style="color: var(--text);">System Settings</h2>
            <div class="user-info" style="color: var(--accent); font-weight: 600;">
                Admin Portal
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card"
                    style="background: var(--card); border: 1px solid #1f2832; border-radius: var(--radius); padding: 20px;">
                    <h3 style="color: var(--accent); margin-top: 0;">Payment QR Scanner</h3>
                    <p style="color: var(--muted); font-size: 14px;">Upload the QR code that users will see on their fund
                        request page.</p>

                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div
                            style="background: rgba(0, 200, 150, 0.2); color: #00c896; padding: 10px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #00c896;">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('updateQrScanner') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 14px;">Current Scanner Image</label>
                            <div style="margin-top: 10px; text-align: center; background: #0f1620; padding: 15px; border-radius: 12px; border: 1px solid #1f2832;">
                                @if (isset($settings) && $settings->qr_scanner_img)
                                    <img src="{{ asset($settings->qr_scanner_img) }}" alt="QR Scanner"
                                        style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--accent);">
                                @else
                                    <div style="color: var(--muted); padding: 20px;">
                                        <div style="font-size: 40px; margin-bottom: 10px;">📷</div>
                                        No QR Image Uploaded
                                    </div>
                                @endif
                            </div>
                        </div> --}}

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label
                                style="display: block; margin-bottom: 12px; color: var(--muted); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                Live QR Preview
                            </label>

                            <div class="qr-main-container">
                                @if (isset($settings) && $settings->qr_scanner_img)
                                    <div class="qr-wrapper" onclick="openFullView()">
                                        <div class="qr-overlay">
                                            <i class="fas fa-expand-alt"></i>
                                            <span>VIEW LARGE</span>
                                        </div>
                                        <img src="{{ asset($settings->qr_scanner_img) }}" id="qr-source" alt="QR Scanner">
                                    </div>
                                    <p class="enlarge-text"><i class="fas fa-info-circle"></i> Click on image to enlarge</p>
                                @else
                                    <div class="qr-empty-state">
                                        <div class="icon">📸</div>
                                        <p>No QR Scanner Uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div id="qrFullModal" class="qr-modal" onclick="closeFullView()">
                            <div class="modal-close">&times;</div>
                            <div class="modal-content-wrapper">
                                <img id="qr-large-display" src="">
                                <div class="modal-caption">Payment Gateway QR</div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="qr_image"
                                style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 14px;">Select New
                                QR Image</label>
                            <input type="file" name="qr_image" id="qr_image" accept="image/*" required
                                style="width: 100%; padding: 10px; background: #0f1620; border: 1px solid #2a3442; border-radius: 8px; color: #fff;">
                        </div>

                        <button type="submit" class="send-btn"
                            style="width: 100%; background: var(--accent); color: #000; border: none; padding: 12px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            Update Scanner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
function openFullView() {
    const modal = document.getElementById("qrFullModal");
    const sourceImg = document.getElementById("qr-source");
    const targetImg = document.getElementById("qr-large-display");
    
    if(sourceImg && targetImg) {
        targetImg.src = sourceImg.src;
        modal.style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling
    }
}

function closeFullView() {
    document.getElementById("qrFullModal").style.display = "none";
    document.body.style.overflow = "auto"; // Re-enable scrolling
}

// Close on Esc
document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") closeFullView();
});
</script>