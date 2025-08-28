<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #212529;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .content {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>{{ config('app.name') }}</h4>
        <a href="#">🏠 Dashboard</a>
        <a href="{{ route('users_data')}}">👤 Profile</a>
        <a href="#">📊 Reports</a>
        <a href="#">⚙️ Settings</a>
        <hr>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger w-100">Logout</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        <!-- <h2>Welcome, {{ Auth::user()->name ?? 'User' }} 👋</h2> -->
        <p class="text-muted">This is your dashboard page.</p>

            @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
