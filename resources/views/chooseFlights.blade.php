@extends('layouts.app')
@section('title', 'Escoge tus vuelos')
@section('header')
    {{-- searchbox inside dropdown --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    {{-- DatePicker --}}
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.11/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.11/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/css/reserve.css">
@endsection

@section('scripts')
    {{-- searchbox inside dropdown --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>$('.select2').select2();</script>
@endsection

@section('content')
    @foreach ($routesFound as $routes)

        <form action="/reserve/storeChosenFlights" method="POST">
        <table class="table table-condensed" style="border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha salida</th>
                        <th>Fecha llegada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($routes as $flight)
                    <input type="hidden" name="id{{$flight->id}}" value="{{$flight->id}}">
                    <tr data-toggle="collapse" class="accordion-toggle">
                        <th scope="row">#</th>
                        <td>{{$flight->origen}}</td>
                        <td>{{$flight->destino}}</td>
                        <td>{{$flight->fecha_salida}}</td>
                        <td>{{$flight->fecha_llegada}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <input type="submit" value="Seleccionar">
            </table>
        </form>

    @endforeach
@endsection
