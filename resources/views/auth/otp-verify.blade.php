<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.head-login')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<style>
    .form-floating {
        position: relative;
        margin-bottom: 1rem;
    }

    .otp-input {
        width: 40px;
        height: 50px;
        font-size: 24px;
        text-align: center;
        margin: 5px;
        border: 2px solid #ced4da;
        border-radius: 8px;
    }

    .otp-input:focus {
        border-color: #007bff;
        outline: none;
    }

    footer.footer {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 1000;
    }

    @media (max-height: 500px) {
        footer.footer {
            display: none;
        }
    }

    .btn-verify {
        background: linear-gradient(195deg, #dc3545, #dc3545);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
        margin-top: 20px;
    }

    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(236, 64, 122, 0.4);
    }

    .btn-verify:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-resend {
        background: transparent;
        border: 2px solid #dc3545;
        color: #dc3545;
        padding: 10px 30px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
        margin-top: 10px;
    }

    .btn-resend:hover {
        background: #dc3545;
        color: white;
    }

    .btn-resend:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .container-main {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .container-main header {
        font-size: 24px;
        font-weight: 700;
        color: #344767;
        text-align: center;
    }

    .title img {
        max-width: 200px;
        margin-bottom: 20px;
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .wrapper {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
    }

    .otp-inputs {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .otp-instruction {
        text-align: center;
        color: #7b809a;
        margin: 15px 0;
    }
</style>

<body class="d-flex flex-column min-vh-100">
    <main class="d-flex flex-grow-1 align-items-center justify-content-center">
        <section class="flex-grow-1">
            <div class="page-header min-vh-75">
                <div class="wrapper">
                    <div class="title text-center mb-4">
                        <img src="{{ asset('img/logo-kayaba.png') }}" alt="Kayaba Indonesia">
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger text-dark text-center">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success text-dark text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger text-dark text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row no-gutters d-flex flex-column align-items-center">
                        <div class="container-main shadow-lg">

                            <div class="bottom w-100">
                                <header class="mb-3">VERIFIKASI OTP</header>

                                <div class="otp-instruction">
                                    Masukkan 6 digit kode OTP<br>
                                    <small>Kode OTP berlaku selama 5 menit</small>
                                </div>

                                <form method="POST" action="{{ route('otp.verify.submit') }}" id="otpForm">
                                    @csrf

                                    <div class="otp-inputs">
                                        @for ($i = 0; $i < 6; $i++)
                                            <input type="text" class="otp-input" name="otp_digit_{{ $i }}"
                                                maxlength="1" data-index="{{ $i }}" autocomplete="off">
                                        @endfor
                                    </div>
                                    <input type="hidden" name="otp" id="otpHidden">

                                    <button type="submit" class="btn btn-verify" id="verifyBtn">
                                        <i class="fas fa-check-circle me-2"></i> VERIFIKASI
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('otp.resend') }}" id="resendForm">
                                    @csrf
                                    <button type="submit" class="btn btn-resend" id="resendBtn">
                                        <i class="fas fa-redo me-2"></i> KIRIM ULANG OTP
                                    </button>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </section>
    </main>

    <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
    <footer class="footer text-center mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto text-center">
                    <p class="mb-0 text-secondary">
                        Copyright ©
                        <script>
                            document.write(new Date().getFullYear())
                        </script> PT Kayaba Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
    <!--   Core JS Files   -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.otp-input');
            const otpForm = document.getElementById('otpForm');
            const resendForm = document.getElementById('resendForm');
            const verifyBtn = document.getElementById('verifyBtn');
            const resendBtn = document.getElementById('resendBtn');
            let isSubmitting = false;

            if (inputs[0]) {
                inputs[0].focus();
            }

            window.addEventListener('resize', function() {
                const footer = document.querySelector('footer.footer');
                if (window.innerHeight < 500) {
                    footer.style.display = 'none';
                } else {
                    footer.style.display = 'block';
                }
            });

            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');

                    if (this.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }

                    updateHiddenOtp();

                    if (allInputsFilled() && !isSubmitting) {
                        setTimeout(() => {
                            submitOtpForm();
                        }, 300);
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (this.value === '' && index > 0) {
                            inputs[index - 1].focus();
                        }
                        this.value = '';
                        updateHiddenOtp();
                    }

                    if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                        navigator.clipboard.readText().then(text => {
                            const digits = text.replace(/[^0-9]/g, '').substring(0, 6);
                            digits.split('').forEach((digit, i) => {
                                if (inputs[i]) {
                                    inputs[i].value = digit;
                                }
                            });
                            updateHiddenOtp();
                            if (digits.length === 6 && !isSubmitting) {
                                setTimeout(() => {
                                    submitOtpForm();
                                }, 300);
                            }
                        });
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const text = e.clipboardData.getData('text');
                    const digits = text.replace(/[^0-9]/g, '').substring(0, 6);
                    digits.split('').forEach((digit, i) => {
                        if (inputs[i]) {
                            inputs[i].value = digit;
                        }
                    });
                    updateHiddenOtp();
                    if (digits.length === 6 && !isSubmitting) {
                        setTimeout(() => {
                            submitOtpForm();
                        }, 300);
                    }
                });
            });

            function updateHiddenOtp() {
                let otpValue = '';
                inputs.forEach(input => {
                    otpValue += input.value;
                });
                document.getElementById('otpHidden').value = otpValue;
            }

            function allInputsFilled() {
                return Array.from(inputs).every(input => input.value.length === 1);
            }

            function submitOtpForm() {
                if (isSubmitting) return;

                isSubmitting = true;
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memverifikasi...';

                otpForm.submit();
            }

            otpForm.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                isSubmitting = true;
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memverifikasi...';
            });

            let isResending = false;
            resendForm.addEventListener('submit', function(e) {
                if (isResending) {
                    e.preventDefault();
                    return false;
                }

                isResending = true;
                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...';
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>
