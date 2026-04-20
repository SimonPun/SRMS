<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    da/sneat_backend/assets-path="/sneat_backend/assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Admin - @yield('title')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('backend/images/favicon.ico') }}" />
    <link href="https://unpkg.com/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="/sneat_backend/assets/vendor/fonts/boxicons.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/5489e1503c.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="/sneat_backend/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/sneat_backend/assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/sneat_backend/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/sneat_backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/sneat_backend/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="/sneat_backend/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="/sneat_backend/assets/js/config.js"></script>
    <style>
        .app-page-heading {
            word-break: break-word;
        }

        .app-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .app-form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .app-table {
            min-width: 720px;
        }

        .app-badge-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .request-actions {
            min-width: 140px;
        }

        .request-action-trigger {
            min-height: 2.5rem;
            border-radius: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
        }

        .request-actions .dropdown-menu {
            min-width: 220px;
            border-radius: 0.9rem;
            border: 1px solid #e7eaf3;
            box-shadow: 0 0.5rem 1.25rem rgba(67, 89, 113, 0.12);
            padding: 0.5rem;
        }

        .request-actions .dropdown-header {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #8592a3;
            padding: 0.35rem 0.75rem 0.5rem;
        }

        .request-actions .dropdown-item {
            border-radius: 0.65rem;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .request-actions .dropdown-item small {
            display: block;
            color: #8592a3;
        }

        .notification-menu {
            width: 340px;
            max-height: 420px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .notification-item {
            border: 1px solid #e7eaf3;
            border-radius: 0.75rem;
            padding: 0.65rem 0.75rem;
            margin-bottom: 0.5rem;
            color: inherit;
            text-decoration: none;
            display: block;
            transition: background-color 0.15s ease;
        }

        .notification-item:hover {
            background-color: #f5f7ff;
            color: inherit;
        }

        .notification-link {
            color: inherit;
            text-decoration: none;
            display: block;
        }

        .notification-link:hover {
            color: inherit;
        }

        .notification-unread-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 999px;
            background: #696cff;
            display: inline-block;
            flex: 0 0 auto;
        }

        .notification-item:last-child {
            margin-bottom: 0;
        }

        .notification-note {
            color: #8592a3;
            font-size: 0.78rem;
        }

        @media (max-width: 991.98px) {
            .layout-navbar .navbar-nav.align-items-center {
                display: none !important;
            }

            .layout-navbar .navbar-nav-right {
                width: 100%;
                justify-content: space-between;
            }

            .layout-navbar .navbar-nav.flex-row {
                margin-left: auto;
            }

            .content-wrapper {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }

        @media (max-width: 575.98px) {
            .container-p-y {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .container-xxl {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            .card-header,
            .card-body {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .layout-navbar .avatar img {
                width: 34px !important;
                height: 34px !important;
            }

            .layout-navbar .fw-semibold {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .app-card-actions,
            .app-form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .app-card-actions .btn,
            .app-form-actions .btn {
                width: 100%;
            }

            .input-group.input-group-sm {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .input-group.input-group-sm > .form-select,
            .input-group.input-group-sm > .btn {
                width: 100%;
                border-radius: 0.375rem !important;
            }

            .table-responsive {
                margin-left: -1rem;
                margin-right: -1rem;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .app-table {
                min-width: 640px;
            }

            .request-actions {
                min-width: 120px;
            }

            .notification-menu {
                width: min(92vw, 340px);
            }
        }
    </style>
</head>

<body>
