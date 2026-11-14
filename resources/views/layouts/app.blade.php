<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>

    <!-- Bootstrap 5 CDN (lebih aman & tidak error file hilang) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-semibold" href="/">
            📚 Perpustakaan SMPN 23
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">

            @auth
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('books*') ? 'active' : '' }}" href="/books">Buku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('loans*') ? 'active' : '' }}" href="/loans">Peminjaman</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="/akun">⚙️ {{ Auth::user()->name }}</a>
                </li>
                <li class="nav-item">
                    <form action="/logout" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light ms-lg-2 mt-2 mt-lg-0">
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
            @endauth

            @guest
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/login">Masuk</a>
                </li>
            </ul>
            @endguest

        </div>

    </div>
</nav>

<!-- CONTENT -->
<div class="container py-4">

    @yield('content')

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>