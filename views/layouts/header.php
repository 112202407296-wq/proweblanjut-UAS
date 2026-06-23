<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KopSkuy - E-Commerce</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk icon keranjang -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-darkest: #2C3639;
            --color-dark: #3F4E4F;
            --color-coffee: #A27B5C;
            --color-beige: #DCD7C9;
            --color-white: #FFFFFF;
        }
        
        body {
            background-color: var(--color-beige) !important;
            color: var(--color-darkest);
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: var(--color-dark) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand, .nav-link {
            color: var(--color-white) !important;
        }

        .nav-link:hover {
            color: var(--color-beige) !important;
        }

        .btn-coffee {
            background-color: var(--color-coffee);
            color: var(--color-white);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-coffee:hover {
            background-color: #8c6a4f; /* slightly darker coffee */
            color: var(--color-white);
            transform: translateY(-2px);
        }

        .btn-outline-light-custom {
            border: 2px solid var(--color-white);
            color: var(--color-white);
            transition: all 0.3s ease;
        }

        .btn-outline-light-custom:hover {
            background-color: var(--color-white);
            color: var(--color-dark);
            transform: translateY(-2px);
        }

        .text-coffee {
            color: var(--color-coffee) !important;
        }

        .card {
            background-color: var(--color-white);
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(44, 54, 57, 0.15) !important;
        }
        
        .main-content {
            flex: 1;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        .footer-custom {
            background-color: var(--color-darkest) !important;
            margin-top: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= BASEURL ?? ''; ?>">☕ KopSkuy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link fw-medium" href="<?= BASEURL ?? ''; ?>">Katalog</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <!-- Link ke keranjang Randi -->
                <a href="<?= BASEURL ?? ''; ?>/cart" class="btn btn-outline-light-custom rounded-pill px-4">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
                <a href="<?= BASEURL ?? ''; ?>/auth/login" class="btn btn-coffee rounded-pill px-4 fw-medium">Login</a>
            </div>
        </div>
    </div>
</nav>
<div class="container main-content"> <!-- Kontainer utama dibuka di sini -->
