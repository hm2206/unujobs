<?php
/**
 * Http/Requests/PersonalRequest.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida peticiones
 * 
 * @category Requests
 */
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
        $rules = [
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
            "requisitos" => "required|max:15",
            "bases" => "required|max:10",
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
