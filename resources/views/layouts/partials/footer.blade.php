<footer class="footer mt-auto py-4 bg-light border-top">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-uppercase fw-bold mb-3 text-primary">{{ config('app.name', 'MXH') }}</h5>
                <p class="text-muted small mb-0">Nền tảng kết nối cộng đồng, chia sẻ khoảnh khắc và lan tỏa niềm vui.</p>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-semibold mb-3">Liên kết</h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Về chúng tôi</a></li>
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Điều khoản</a></li>
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Bảo mật</a></li>
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Trợ giúp</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                 <h6 class="text-uppercase fw-semibold mb-3">Hỗ trợ</h6>
                 <ul class="list-unstyled mb-0">
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Trung tâm trợ giúp</a></li>
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Báo cáo sự cố</a></li>
                    <li><a href="#!" class="text-muted text-decoration-none small footer-link">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-semibold mb-3">Theo dõi</h6>
                <p class="text-muted small mb-3">Cập nhật những tin tức và hoạt động mới nhất từ chúng tôi trên các nền tảng mạng xã hội.</p>
                <div class="d-flex">
                    <a href="#" class="text-muted text-decoration-none me-3 social-icon fs-5" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted text-decoration-none me-3 social-icon fs-5" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-muted text-decoration-none me-3 social-icon fs-5" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-muted text-decoration-none social-icon fs-5" title="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Mạng xã hội') }}. Đã đăng ký bản quyền.</p>
        </div>
    </div>
</footer>

{{-- Nút Back to Top --}}
<button id="back-to-top-btn" class="btn btn-primary rounded-circle shadow" title="Lên đầu trang">
    <i class="bi bi-arrow-up"></i>
</button>

{{-- ======= CSS Bổ Sung Cho Footer ======= --}}
<style>
    .footer {
        font-size: 0.9rem;
    }
    .footer h5, .footer h6 {
        letter-spacing: 0.5px;
    }
    .footer-link {
        padding-bottom: 2px;
        transition: color 0.2s ease-in-out, transform 0.2s ease;
        display: inline-block;
    }
    .footer-link:hover {
        color: #0d6efd !important;
        transform: translateX(3px); /* Hiệu ứng di chuyển nhẹ khi hover */
        text-decoration: none !important;
    }
    .social-icon i {
         transition: color 0.2s ease-in-out, transform 0.2s ease;
    }
    .social-icon:hover i {
        color: #0d6efd;
        transform: scale(1.1); /* Icon phóng to nhẹ khi hover */
    }

    /* Style cho nút Back to Top */
    #back-to-top-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: none; /* Ẩn ban đầu */
        z-index: 1000;
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
        line-height: 1;
        padding: 0;
        opacity: 0.8;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #back-to-top-btn:hover {
        opacity: 1;
    }
    #back-to-top-btn.show {
        display: block; /* Hiện nút */
    }
</style> 