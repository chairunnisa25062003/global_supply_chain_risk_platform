<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GSCR Platform</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

@vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body>

<div class="wrapper">

    <aside class="sidebar">

        <div class="logo">

            🌍

            <h3>GSCR</h3>

            <small>Risk Platform</small>

        </div>

        <ul>

            <li>🏠 Dashboard</li>

            <li>🌎 Countries</li>

            <li>🌦 Weather</li>

            <li>📈 Economy</li>

            <li>💱 Currency</li>

            <li>🚢 Ports</li>

            <li>📰 News</li>

            <li>⚠ Risk</li>

            <li>📊 Analytics</li>

            <li>⭐ Watchlist</li>

        </ul>

    </aside>

    <main class="content">

        @yield('content')

    </main>

</div>

</body>

</html>