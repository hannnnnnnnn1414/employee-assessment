<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
    <link rel="icon" type="image/png"
        href="{{ url('https://www.ifpusa.com/wp-content/uploads/2021/11/KYB%20DRUPAL%20LOGO.png') }}">
    <title>
        Kayaba Indonesia - OTP Verification
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Host+Grotesk:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Nucleo Icons -->
    <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="{{ asset('css/login.css') }}" rel="stylesheet" />

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
            width: 50px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            margin: 5px;
            border: 2px solid #ced4da;
            border-radius: 8px;
            font-weight: bold;
        }

        .otp-input:focus {
            border-color: #e91e63;
            outline: none;
            box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.1);
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

        .otp-container {
            max-width: 450px;
            margin: 0 auto;
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
            margin-bottom: 15px;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(236, 64, 122, 0.4);
        }

        .btn-resend {
            background: transparent;
            border: 2px solid #e91e63;
            color: #e91e63;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-resend:hover {
            background: #e91e63;
            color: white;
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

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
        }

        .otp-instruction {
            color: #7b809a;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .otp-testing {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
            border-left: 4px solid #e91e63;
            font-size: 14px;
        }

        .otp-testing p {
            margin: 0;
        }

        .otp-testing strong {
            color: #e91e63;
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
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
