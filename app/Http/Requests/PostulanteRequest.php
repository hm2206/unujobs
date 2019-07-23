<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostulanteRequest extends FormRequest
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
            "ape_paterno" => "required",
            "ape_materno" => "required",
            "nombres" => "required",
            "numero_de_documento" => "required",
            "departamento_id" => "required",
            "provincia_id" => "required",
            "distrito_id" => "required",
            "fecha_de_nacimiento" => "date",
            "phone" => "required",
            "direccion" => "required",
            "email" => "email",
            "personal_id" => "required"
        ];
    }
}
