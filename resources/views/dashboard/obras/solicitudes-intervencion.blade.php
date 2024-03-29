@extends('layouts.dashboard', ['menu' => "solicitudes-intervencion"])

@section('top-body')
    <div class="col-sm-4">
        <h2>Solicitudes de intervención</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('dashboard.dashboard.index') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('dashboard.obras.index') }}">Obras</a>
            </li>
            <li class="active">
                <strong>Solicitudes de intervención</strong>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">
        <div class="title-action">
            @if (Auth::user()->rol->captura_solicitud_obra)
                <btn onclick="crear();" class="btn btn-primary">Crear nueva solicitud de intervención</btn>
            @endif
        </div>
    </div>
@endsection

@section('body')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="sk-spinner sk-spinner-double-bounce" id="carga-dt">
                        <div class="sk-double-bounce1"></div>
                        <div class="sk-double-bounce2"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="dt-datos">
                            <thead>
                                <tr>
                                    <th>Titulo</th>
                                    <th>Tipo bien cultural</th>
                                    <th>Tipo de objeto</th>
                                    <th>Año</th>
                                    <th>Época</th>
                                    <th>Temporalidad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {!! Html::script('scripts/dashboard/obras/solicitudes-intervencion.js') !!}
@endsection