{{-- filepath: resources/views/layouts/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGRISUPPLY Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { margin:0; padding:0; overflow-x:hidden; }

        .container { display:flex; width:100%; min-height:100vh; }

        .navigation {
            position:fixed; top:0; left:0;
            height:100vh; overflow-y:auto; z-index:1000;
            flex-shrink:0;
            transition:width 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        .details {
            position:relative; left:300px;
            width:calc(100% - 300px); min-height:100vh;
            display:flex; flex-direction:column;
            transition:left 0.3s cubic-bezier(0.4,0,0.2,1),
                       width 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        .requestor-wrap { flex:1; display:flex; flex-direction:column; }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')

            @hasSection('content')
                @yield('content')
            @else
                @if(auth()->user()->isRequestor())
                    @include('client.dashboard.requestor')
                @else
                    @include('client.dashboard.index')
                @endif
            @endif
        </div>
    </div>

    @include('layouts.core.footer')
    @stack('scripts')
</body>
</html>