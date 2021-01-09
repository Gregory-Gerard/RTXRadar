<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>RTX Radar</title>

    <!-- je chie sur l'optimisation -->
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">

    <!-- encore -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Fira Code', system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-black bg-fixed text-white">
    <header class="container px-4 mt-4 mb-8 mx-auto">
        <h1 class="text-4xl font-bold">
            @isset($product)
                <a href="{{ route('page.index') }}">&larr;</a>
                {{ $product->title }}
            @else
                RTX Radar
            @endisset
        </h1>
        <p>Puisse le sort vous être favorable</p>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="container px-4 mx-auto mt-8 text-sm text-gray-600">
        &copy; {{ date('Y') }} GG
    </footer>

    <script>
        const getPillColor = (state) => {
            switch (state) {
                case 'yes':
                    return 'bg-green-400';
                case 'soon':
                    return 'bg-yellow-400';
                default:
                    return 'bg-red-400';
            }
        }

        const getStockText = (state) => {
            switch (state) {
                case 'yes':
                    return 'En stock';
                case 'soon':
                    return 'Arrivage';
                default:
                    return 'En rupture';
            }
        }
    </script>

    @stack('js')
</body>
</html>
