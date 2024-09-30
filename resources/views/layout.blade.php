<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Styles -->
        {{-- <link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/22.2.12/css/dx.light.css"> --}}
        <link href="{{ asset('js/devextreme/22.2.12/dx.light.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        <script src="{{ asset('js/jquery/3.7.1/min.js') }}"></script> 
        {{-- <script type="text/javascript" src="https://cdn3.devexpress.com/jslib/22.2.12/js/dx.all.js"></script> --}}
        <script type="text/javascript" src="{{ asset('js/devextreme/22.2.12/dx.all.js') }}"></script>
        {{-- <script type="text/javascript" src="https://cdn3.devexpress.com/jslib/22.2.12/js/localization/dx.messages.es.js"></script> --}}
        <script type="text/javascript" src="{{ asset('js/devextreme/22.2.12/dx.messages.es.js') }}"></script>

        <script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script> 
        <script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script> 
        <script src="{{ asset('js/devextreme/jspdf.umd.min.js') }}"></script> 
        <script src="{{ asset('js/devextreme/jspdf.plugin.autotable.min.js') }}"></script> 

    </head>
    <body class="antialiased">
        <header>
            <select id="pageSelector" onchange="redirectToPage()">
                <option value="">Selecciona una página</option>
                <option value="/" {{(request()->routeIs('todojunto')) ? 'selected':''}}>todo junto</option>
                <option value="/javascript" {{(request()->routeIs('javascript')) ? 'selected':''}}>En javascript</option>
                <option value="/agrupacion" {{(request()->routeIs('agrupacion')) ? 'selected':''}}>Con agrupacion</option>
                <option value="/maestrodetalle" {{(request()->routeIs('maestrodetalle')) ? 'selected':''}}>Con maestro detalle</option>
                <option value="/maestrodetalle_detalle_aparte" {{(request()->routeIs('maestrodetalle_detalle_aparte')) ? 'selected':''}}>Con maestro detalle separado</option>
            </select>
        </header>
        <div>
            @yield('content')
        </div>
    </body>
    <script>
        function redirectToPage() {
            const selector = document.getElementById("pageSelector");
            const selectedPage = selector.value;
            if (selectedPage) {
                window.location.href = selectedPage;
            }
        }

        function sendRequest(url, method, data) { // función para solicitudes de devxtreme
            var d = $.Deferred();
            method = method || "GET";
            $.ajax(url, {
                method: method || "GET",
                data: data,
                cache: false,
                xhrFields: {
                    withCredentials: true
                }
            }).done(function (result) {
                /* d.resolve(method === "GET" ? result.data : result); */
                d.resolve(result);
            }).fail(function (xhr) {
                // showNotificacion('error', 'Error al obtener los datos');
                d.reject(xhr.responseJSON ? xhr.responseJSON.Message : xhr.statusText);
            });
            return d.promise();
        }
    </script> 
    @yield('js')
</html>
