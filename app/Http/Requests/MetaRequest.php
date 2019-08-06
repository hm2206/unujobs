<?php
/**
 * Http/Requests/MetaRequest.php
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
