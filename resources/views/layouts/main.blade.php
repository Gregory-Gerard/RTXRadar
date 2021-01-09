<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>RTX Radar</title>
    <meta name="description" content="Suivre en temps réel les stocks des dernières cartes graphiques (série 30)">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="RTX Radar">
    <meta property="og:description" content="Suivre en temps réel les stocks des dernières cartes graphiques (série 30)">
    <meta property="og:image" content="{{ asset('/card.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="RTX Radar">
    <meta property="twitter:description" content="Suivre en temps réel les stocks des dernières cartes graphiques (série 30)">
    <meta property="twitter:image" content="{{ asset('/card.jpg') }}">

    <!-- je chie sur l'optimisation -->
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">

    <!-- encore -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Fira Code', system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        }

        .onesignal-customlink-subscribe {
            box-shadow: none !important;
        }

        .onesignal-customlink-subscribe:hover {
            background-color: rgb(49, 42, 148) !important;
        }
    </style>

    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "7d7dcbef-fd36-4088-bffd-df05736cc136",
                allowLocalhostAsSecureOrigin: true
            });
        });
    </script>
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

        <div class="onesignal-customlink-container mt-2"></div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="container px-4 mx-auto mt-8 text-sm text-gray-600">
        &copy; {{ date('Y') }} GG — <a href="https://github.com/Gregory-Gerard/RTXRadar" target="_blank" class="hover:underline">Github</a>
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
