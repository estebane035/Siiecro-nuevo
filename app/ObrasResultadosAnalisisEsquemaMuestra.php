<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObrasResultadosAnalisisEsquemaMuestra extends Model
{
    protected $table = 'obras__resultados_analisis_esquema_muestra';
    protected $fillable = [
    	'id',
    	'resultado_analisis_id',
        'imagen',
    ];

    public function resultados_analisis() {
        return $this->hasOne('App\ObrasResultadosAnalisis', 'id', 'resultado_analisis_id');
    }
}
