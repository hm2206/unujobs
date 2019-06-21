<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "numero_de_requerimiento" => "required",
            "sede_id" => "required",
            "dependencia_id" => "required",
            "cargo_id" => "required",
            "cantidad" => "required",
            "honorarios" => "required",
            "meta_id" => "required",
            "fuente_id" => "required",
            "gasto" => "required",
            "periodo" => "required",
            "lugar_id" => "required",
            "perfil" => "required"  
        ];
    }
}
