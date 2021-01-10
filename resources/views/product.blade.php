@extends('layouts.main')

@section('content')
    <div class="container px-4 mx-auto">
        <header class="pb-6 border-b border-gray-800"></header>

        <ul class="mt-6 space-y-4"></ul>
    </div>
@endsection

@push('js')
    <script>
        const retrieveProductItems = () => {
            fetch('{{ route('api.products.show', $product->id) }}')
                .then(response => response.json())
                .then(response => parseProductItems(response))
                .catch(error => console.error("Erreur : " + error));
        }

        const parseProductItems = (response) => {
            document.querySelector('main header').innerHTML = `
                <div class="flex items-center space-x-4"><span class="w-4 h-4 rounded-full ${getPillColor(response.data.items.state)} flex-shrink-0"></span> <span>${getStockText(response.data.items.state)}, ${response.data.items.count} pièces suivies, dernière mise à jour le ${new Date(response.data.items.updated_at).toLocaleString()}</span></div>
            `;

            document.querySelector('main ul').innerHTML = response.data.items.data.map(item => `
                <li>
                    <a href="${item.url}" target="_blank" class="flex items-center space-x-4 rounded bg-gray-800 hover:bg-gray-800 bg-opacity-25 transition-colors px-4 py-1 shadow-md">
                        <span class="w-4 h-4 rounded-full ${getPillColor(item.state)} flex-shrink-0"></span>
                        <div class="flex flex-col w-full min-w-0">
                            <strong class="text-lg truncate">${item.title}</strong>
                            <small>${item.seller} — <span class="hidden md:inline">dernière mise à jour le</span> ${new Date(item.updated_at).toLocaleString()}</small>
                        </div>
                        <strong class="text-lg ml-4">${(item.price/100).toFixed(2)}&nbsp;€</strong>
                    </a>
                </li>
            `).join('');
        }

        setInterval(retrieveProductItems, 10 * 1000);

        retrieveProductItems();
    </script>
@endpush
