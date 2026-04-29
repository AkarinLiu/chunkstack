<x-frontend-layouts.app>
    @php
        $initialData = [
            'link' => $link->load('category', 'tags'),
        ];
    @endphp
    <div id="app"></div>
    <script>
        window.__INITIAL_DATA__ = @json($initialData);
    </script>
</x-frontend-layouts.app>
