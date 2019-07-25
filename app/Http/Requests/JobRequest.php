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
            "numero_de_documento" => "required",
            "fecha_de_nacimiento" => "date",
            "fecha_de_ingreso" => "required",
            "sexo" => "required",
            "accidentes" => "required",
            "phone" => "required",
            "direccion" => "required",
            "profesion" => "required"
        ];
    }
}
