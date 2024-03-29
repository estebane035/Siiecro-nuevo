<div class="modal inmodal" id="modal-crear" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Obras | Detalle | Solicitudes de Análisis</h4>
                <small class="font-bold">{{ $registro == "[]" ? "Creando nueva Solicitud de Análisis" : "Editando solicitud " }} <strong>{{ $registro->tecnica }}</strong></small>
            </div>
            @if ($registro == "[]")
                {!! Form::open(['route' => ['dashboard.solicitudes-analisis.store'], 'method' => 'POST', 'id' => 'form-obras-detalle-solicitudes-analisis', 'class' => 'form-horizontal']) !!}
            @else
                {!! Form::open(['route' => ['dashboard.solicitudes-analisis.update', $registro->id], 'method' => 'PUT', 'id' => 'form-obras-detalle-solicitudes-analisis', 'class' => 'form-horizontal']) !!}
            @endif
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-8 div-input required">
                                <label for="tecnica">Técnica de manufactura</label>
                                <input type="text" class="form-control" id="tecnica" name="tecnica" value="{{ $registro->tecnica }}" required autocomplete="off">
                            </div>
                            <div class="col-md-4 div-input required">
                                <label for="fecha_intervencion">Fecha de intervención</label>
                                <input type="text" class="form-control" id="fecha_intervencion" name="fecha_intervencion" value="{{ $registro->fecha_intervencion }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 div-input required">
                                <label for="obra_usuario_asignado_id">Responsable</label>
                                <select class="form-control select2" id="obra_usuario_asignado_id" name="obra_usuario_asignado_id" required autocomplete="off">
                                    <option value=""></option>
                                    @foreach ($responsables_intervencion as $responsable_intervencion)
                                        <option {{ $responsable_intervencion->id == $registro->obra_usuario_asignado_id ? "selected" : "" }} value="{{ $responsable_intervencion->id }}">{{ $responsable_intervencion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 div-input required">
                                <label for="obra_temporada_trabajo_asignada_id">Temporada trabajo</label>
                                <select class="form-control select2" id="obra_temporada_trabajo_asignada_id" name="obra_temporada_trabajo_asignada_id" required autocomplete="off">
                                    <option value=""></option>
                                    @foreach ($temporadasTrabajoAsignadas as $temporadaTrabajoAsignada)
                                        <option {{ $temporadaTrabajoAsignada->id == $registro->obra_temporada_trabajo_asignada_id ? "selected" : "" }} value="{{ $temporadaTrabajoAsignada->id }}">{{ $temporadaTrabajoAsignada->temporada_trabajo->numero_temporada }} [{{ $temporadaTrabajoAsignada->temporada_trabajo->año }}]</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($registro != "[]")
                            <div class="row">
                                <div class="col-md-12 div-input required">
                                    <label for="dropzone-solicitud-analisis">Esquema</label>
                                    <div class="dropzone " id="dropzone-solicitud-analisis">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row m-t-md center-block">
                        @include('dashboard.obras.detalle.solicitudes-analisis.esquema.ver', ["imagenes_esquema" => $registro->imagenes_esquema])
                    </div>

                    <div class="row m-t-md" id="div-notificacion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>

                <input type="hidden" id="obra_id" name="obra_id" value="{{ $registro != "[]" ? $registro->obra_id : ''}}">
            {!! Form::close() !!}
        </div>
    </div>
</div>