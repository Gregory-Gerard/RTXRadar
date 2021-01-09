@extends('layouts.main')

@section('content')
    <div class="container px-4 mx-auto">
        <ul class="space-y-4"></ul>
    </div>
@endsection

@push('js')
    <script>
        const retrieveProductList = () => {
            fetch('{{ route('api.products.index') }}')
                .then(response => response.json())
                .then(response => parseProductList(response))
                .catch(error => alert("Erreur : " + error));
        }

        const parseProductList = (response) => {
            document.querySelector('main ul').innerHTML = response.data.map(product => `
                <li>
                    <a href="${product.url}" class="flex items-center space-x-4 rounded bg-gray-800 hover:bg-gray-800 bg-opacity-25 transition-colors px-4 py-2 shadow-md">
                        <span class="w-4 h-4 rounded-full ${getPillColor(product.items.state)} flex-shrink-0"></span>
                        <strong class="text-2xl flex-shrink-0">${product.title}</strong>
                        <small>${product.items.count} <span class="hidden md:inline">pièces suivies</span> — <span class="hidden md:inline">dernière mise à jour le</span> ${new Date(product.items.updated_at).toLocaleString()}</small>
                    </a>
                </li>
            `).join('');
        }

        setInterval(retrieveProductList, 10 * 1000);

        retrieveProductList();
    </script>
@endpush
