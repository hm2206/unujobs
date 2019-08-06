<?php
/**
 * Http/Requests/ConvocatoriaRequest.php
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
class ConvocatoriaRequest extends FormRequest
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
            "numero_de_convocatoria" => "required",
            "fecha_inicio" => "date",
            "fecha_final" => "required",
        ];
    }
}
