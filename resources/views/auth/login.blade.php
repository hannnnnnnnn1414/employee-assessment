<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.head-login')
</head>

<style>
    .form-floating {
        position: relative;
        margin-bottom: 1rem;
    }

    .form-floating input:focus~label,
    .form-floating input:not(:placeholder-shown)~label {
        top: -0.75rem;
        font-size: 0.75rem;
        color: #007bff;
    }

    .form-floating label {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        pointer-events: none;
        transition: all 0.2s;
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

    .login-container {
        max-width: 400px;
        margin: 0 auto;
    }

    .btn-login {
        background: linear-gradient(195deg, #dc3545, #dc3545);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(236, 64, 122, 0.4);
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

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        color: #7b809a;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border: 1px solid #d2d6da;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #e91e63;
        box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.1);
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
                            @if ($errors->has('npk'))
                                NPK atau password salah, silakan coba lagi.
                            @else
                                Terjadi kesalahan, silakan coba lagi.
                            @endif
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
                                <header class="mb-4">SIGN IN</header>
                                <form method="POST" action="{{ route('login.submit') }}">
                                    @csrf

                                    <!-- NPK -->
                                    <div class="form-group">
                                        <label class="form-label" for="npk">NPK</label>
                                        <br>
                                        <input class="form-control" id="npk" type="text" name="npk"
                                            value="{{ old('npk') }}" required autofocus placeholder="Masukkan NPK">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label" for="password">Password</label>
                                        <br>
                                        <input class="form-control" id="password" type="password" name="password"
                                            required placeholder="Masukkan Password">
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-login">
                                            <i class="fas fa-sign-in-alt me-2"></i> LOGIN
                                        </button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <small class="text-muted">Employee Assessment System</small>
                                    </div>

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
            window.addEventListener('resize', function() {
                const footer = document.querySelector('footer.footer');
                if (window.innerHeight < 500) {
                    footer.style.display = 'none';
                } else {
                    footer.style.display = 'block';
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>
