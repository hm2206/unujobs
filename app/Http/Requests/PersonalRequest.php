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


    public function rules()
    {
        $rules = [
            "numero_de_requerimiento" => "required",
            "sede_id" => "required",
            "dependencia_txt" => "required",
            "cargo_txt" => "required",
            "cantidad" => "required|max:2",
            "honorarios" => "required",
            "meta_id" => "required",
            "deberes" => "required",
            "fecha_inicio" => "required|date",
            "fecha_final" => "required|date",
            "lugar_txt" => "required",
            "supervisora_txt" => "required",
            "titulos" => "required",
            "requisitos" => "required",
            "bases" => "required|max:10",
            "titulos" => "required|max:15"
        ];

        foreach ($this->request->get('bases') as $key => $value) {
            $rules['bases.'.$key] = 'required';
        }

        foreach ($this->request->get('requisitos', []) as $key => $req) {
            $titulo = isset($req[0]) ? $req[0] : "";
            $body = isset($req[1]) ? $req[1] : [];

            if(!$titulo) {
                $rules['titulos.'.$key] = 'required|max:255';
            }

            foreach ($body as $b => $bb) {
                if (!$bb) {
                    $rules['body.'.$key.'.'.$b] = 'required|max:255';
                }
            }

        }

        return $rules;
    }
}
