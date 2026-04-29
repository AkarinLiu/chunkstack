<x-frontend-layouts.app>
    @php
        $initialData = [
            'query' => $query ?? null,
            'links' => $links->values(),
            'categories' => $categories->values(),
            'sort' => $sort ?? 'sort_order',
            'direction' => $direction ?? 'asc',
        ];
    @endphp
    <div id="app"></div>
    <script>
        window.__INITIAL_DATA__ = @json($initialData);
    </script>
</x-frontend-layouts.app>
