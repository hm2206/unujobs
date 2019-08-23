<?php
/**
 * Http/Controllers/ImportController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Jobs\ImportQueue;
use App\Imports\WorkImport;
use App\Imports\RemuneracionImport;
use App\Imports\DescuentoImport;
use App\Imports\EtapaImport;
use App\Models\Cronograma;
use App\Models\TypeEtapa;
use App\Imports\MetaImport;
use App\Imports\CategoriaImport;
use App\Imports\CategoriaConceptoImport;
use App\Imports\WorkConfigImport;
use App\Models\Personal;

/**
 * Class ImportController
 * 
 * @category Controllers
 */
class ImportController extends Controller
{

    public function __construct()
    {
        $this->middleware('import')->only('descuento', 'remuneracion');
    }
    
    
    /**
     * Realizar la importacion de trabajadores desde un archivo excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function work(Request $request)
    {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);
        
        try {
            // configurar archivo de excel
            $name = "work_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new WorkImport)->import("/imports/{$name}", "public");
    
            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    /**
     * Realizar la importación de las remuneraciones con respecto al cronograma, desde un archivo de excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remuneracion(Request $request, $slug)
    {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);

        try {
            // recuperar id
            $id = \base64_decode($slug);
            // obtener cronograma
            $cronograma = Cronograma::where("estado", 1)
                ->where("pendiente", 0)
                ->findOrFail($id);
            // configurar archivo de excel
            $name = "remuneracion_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new RemuneracionImport($cronograma, $name))->import("/imports/{$name}", "public");
    
            return [
                "status" => true,
                "message" => "La importación ha sido exitosa"
            ];

        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "La importación falló, El formato es incorrecto!"
            ]; 
        }
    }

    /**
     * Realizar la importación de los descuentos con respecto al cronograma, desde un archivo de excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function descuento(Request $request, $slug)
    {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);

        try {
            // recuperar id
            $id = \base64_decode($slug);
            // obtener cronograma
            $cronograma = Cronograma::where("estado", 1)
                ->where("pendiente", 0)
                ->findOrFail($id);
            // configurar archivo de excel
            $name = "descuento_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new DescuentoImport($cronograma, $name))->import("/imports/{$name}", "public");
    
            return [
                "status" => true,
                "message" => "La importación ha sido exitosa"
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "La importación falló"
            ]; 
        }

    }


    /**
     * Realizar la importación de evaluacion y selección de cada etapa, desde un archivo de excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function etapa(Request $request, $id) {
        $this->validate(request(), [
            "import" => "required|file|max:1024",
            "personal_id" => "required"
        ]);

        try {

            // obtener la etapa
            $type = TypeEtapa::findOrFail($id);
            // obtener la etapa
            $personal = Personal::findOrFail($request->personal_id);
            // configurar archivo de excel
            $name = "etapa_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new EtapaImport($type, $personal, $name))->import("/imports/{$name}", "public");

            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    public function meta(Request $request) {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);

        try {
            // configurar archivo de excel
            $name = "meta_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new MetaImport)->import("/imports/{$name}", "public");

            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    public function categoria(Request $request) {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);

        try {
            // configurar archivo de excel
            $name = "categoria_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new CategoriaImport)->import("/imports/{$name}", "public");

            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }



    public function categoriaConcepto(Request $request) {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);

        try {
            // configurar archivo de excel
            $name = "categoria_concepto_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new CategoriaConceptoImport)->import("/imports/{$name}", "public");

            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    public function workConfig(Request $request) {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);

        try {
            // configurar archivo de excel
            $name = "work_config_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new WorkConfigImport)->import("/imports/{$name}", "public");

            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


}
