<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
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
            "ape_paterno" => "required",
            "ape_materno" => "required",
            "nombres" => "required",
            "numero_de_documento" => "required|unique:jobs,numero_de_documento" . self::update("", ",".request()->id),
            "fecha_de_nacimiento" => "date",
            "profesion" => "required",
            "phone" => "required",
            "fecha_de_ingreso" => "required",
            "sexo" => "required",
            "numero_de_essalud" => "required|unique:jobs,numero_de_essalud" . self::update("", ",".request()->id),
            "banco_id" => "required",
            "numero_de_cuenta" => "required|unique:jobs,numero_de_cuenta" . self::update("", ",".request()->id),
            "afp_id" => "required",
            "fecha_de_afiliacion" => "date",
            "numero_de_cussp" => "required",
            "accidentes" => "required",
            "categoria_id" => "required",
        ];
    }
}
