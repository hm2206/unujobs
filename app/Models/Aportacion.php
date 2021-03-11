<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aportacion extends Model
{
    

    protected $fillable = [
        'work_id', 'info_id', 'historial_id', 'type_descuento_id',
        'type_aportacion_id', 'porcentaje', 'minimo', 'default',
        'monto', "cronograma_id"
    ];


    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    /**
     * Relación con la tabla info
     *
     * @return void
     */
    public function info()
    {
        return $this->belongsTo(Info::class);
    }

    /**
     * Relación con la tabla historial
     *
     * @return void
     */
    public function historial()
    {
        return $this->belongsTo(Historial::class);
    }

    /**
     * Relación con la tabla type_descuentos
     *
     * @return void
     */
    public function type_descuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }

    /**
     * Relación con la tabla type_aportacions
     *
     * @return void
     */
    public function type_aportacion()
    {
        return $this->belongsTo(TypeAportacion::class);
    }


}
