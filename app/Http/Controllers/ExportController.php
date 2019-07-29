<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\WorkExport;
use App\Exports\MetaExport;
use App\Exports\CargoExport;
use App\Exports\CategoriaExport;
use App\Exports\CronogramaExport;
use App\Jobs\ExportQueue;


class ExportController extends Controller
{
    
    public function work(Request $request)
    {
        $this->validate(request(), [
            "limite" => "required|numeric"
        ]);

        $limite = round($request->limite, 0);
        $order = $request->order ? 'DESC' : 'ASC';
        $name = 'work_' . date('Y-m-d') . '.xlsx';
        $ruta = "public/excels/{$name}";
            
        (new WorkExport($limite, $order))->queue($ruta)->chain([
            new ExportQueue("/storage/excels/{$name}", $name)
        ]);

        return back()->with(["success" => "Le notificaremos cuando la exportación esté lista"]);
    }

    public function meta(Request $request)
    {
        $this->validate(request(), [
            "limite" => "required|numeric"
        ]);

        $limite = round($request->limite, 0);
        $order = $request->order ? 'DESC' : 'ASC';
        $name = 'meta_' . date('Y-m-d') . '.xlsx';
        $ruta = "public/excels/{$name}";
            
        (new MetaExport($limite, $order))->queue($ruta)->chain([
            new ExportQueue("/storage/excels/{$name}", $name)
        ]);

        return back()->with(["success" => "Le notificaremos cuando la exportación esté lista."]);
    }

    public function cargo()
    {
        $name = 'cargo_' . date('Y-m-d') . '.xlsx';
        $ruta = "public/excels/{$name}";
            
        (new CargoExport)->queue($ruta)->chain([
            new ExportQueue("/storage/excels/{$name}", $name)
        ]);

        return back()->with(["success" => "Le notificaremos cuando la exportación esté lista."]);
    }

    public function categoria()
    {
        $name = 'categoria_' . date('Y-m-d') . '.xlsx';
        $ruta = "public/excels/{$name}";
            
        (new CategoriaExport)->queue($ruta)->chain([
            new ExportQueue("/storage/excels/{$name}", $name)
        ]);

        return back()->with(["success" => "Le notificaremos cuando la exportación esté lista."]);
    }

    public function cronograma(Request $request, $id)
    {
        $name = 'cronograma_' . date('Y-m-d') . '.xlsx';
        $ruta = "public/excels/{$name}";
            
        (new CronogramaExport($id))->queue($ruta)->chain([
            new ExportQueue("/storage/excels/{$name}", $name)
        ]);

        return back()->with(["success" => "Le notificaremos cuando la exportación esté lista."]);
    }

}
