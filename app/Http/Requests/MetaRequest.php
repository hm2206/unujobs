<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    public function update($default, $newValue)
    {
        return request()->_method == "PUT" ? $newValue : $default;
    } 


    public function rules()
    {
        return [
            "metaID" => "required|unique:metas,metaID" . self::update("", ",".request()->metaID),
            "meta" => "required|unique:metas,meta". self::update("", ",".request()->metaID),
            "sectorID" => "required",
            "sector" => "required",
            "pliegoID" => "required",
            "pliego" => "required",
            "unidadID" => "required",
            "unidad_ejecutora" => "required",
            "programa" => "required",
            "programaID" => "required",
            "funcionID" => "required",
            "funcion" => "required",
            "subProgramaID" => "required",
            "sub_programa" => "required",
            "actividadID" => "required",
            "actividad" => "required",
        ];
    }
}
