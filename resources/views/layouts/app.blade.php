<!DOCTYPE html>
<html>
<head>
    <title>SEO Advisor</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main -->
    <div class="flex-1">

        @include('components.header')

        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
