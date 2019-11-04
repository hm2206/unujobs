<?php
/**
 * Http/Requests/JobRequest.php
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

    /**
     * Undocumented function
     *
     * @param string $default
     * @param string $newValue
     * @return void
     */
    public function update($default, $newValue)
    {
        return request()->_method == "PUT" ? $newValue : $default;
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
            "fecha_de_nacimiento" => "date",
            "sexo" => "required",
            "phone" => "required",
            "direccion" => "required",
            "profesion" => "required"
        ];
    }
}
