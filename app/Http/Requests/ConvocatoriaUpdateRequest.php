<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvocatoriaUpdateRequest extends FormRequest
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
        $rules = [
            "numero_de_convocatoria" => "required",
            "fecha_inicio" => "required|date",
            "fecha_final" => "required|date"
        ];


        foreach ($this->request->get('activities', []) as $key => $value) {
            $titulo = $value[1];
            $f_inicio = $value[2];
            $f_final = $value[3];
            $responsable = $value[4];

            
            if ($titulo == "") {
                $rules["activity.{$key}.1"] = "required";
            }

            if ($f_inicio == "") {
                $rules["activity.{$key}.2"] = "required|date";
            }

            if ($responsable == "") {
                $rules["activity.{$key}.4"] = "required|date";
            }
        }


        if ($this->request->get('observacion', "")) {
            $rules["observacion"] = "max:255";
        }

        return $rules;
    }
}
