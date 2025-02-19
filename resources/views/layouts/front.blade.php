<!doctype html>
<html lang="en">

    @include('partial.frontend.header')

    <body>
        @include('partial.frontend.navbar')
        @yield('content')
        @include('partial.frontend.footer')
    </body>

</html>
