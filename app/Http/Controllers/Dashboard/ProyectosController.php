<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Areas;
use App\Proyectos;

use DataTables;
use BD;
use Response;
use Hash;
use Auth;


class ProyectosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('VerificarPermiso:captura_de_catalogos_avanzada', ['except' => ["select2"]]);
        $this->middleware('VerificarPermiso:eliminar_catalogos',    ["only" =>  ["eliminar", "destroy"]]);
    }
    
    public function index(){
        $titulo         =   "Proyectos";
        
        return view("dashboard.proyectos.index", ["titulo" => $titulo]);
    }

    public function cargarTabla(Request $request){
        $busqueda       =   $request->input("search")["value"];
        $registros      =   Proyectos::selectRaw("
                                                    proyectos.*,
                                                    a.nombre    as nombre_area
                                                ")
                                        ->join('areas as a', 'a.id', 'proyectos.area_id');

        return DataTables::of($registros)
                        ->editColumn('id', function($registro){
                            return $registro->etiquetaFolio();
                        })
                        ->addColumn('acciones', function($registro){
                            $editar         =   '<i onclick="editar('.$registro->id.')" class="fa fa-pencil fa-lg m-r-sm pointer inline-block" aria-hidden="true" mi-tooltip="Editar"></i>';
                            $eliminar       =   '<i onclick="eliminar('.$registro->id.')" class="fa fa-trash fa-lg m-r-sm pointer inline-block" aria-hidden="true" mi-tooltip="Eliminar"></i>';
                            $ver            =   '<a class="icon-link" href="'.route('dashboard.proyectos.show', $registro->id).'"><i class="fa fa-search fa-lg m-r-sm pointer inline-block" aria-hidden="true" mi-tooltip="Ver temporadas trabajo"></i></a>';

                            return $ver.$editar.$eliminar;
                        })
                        ->filter(function($query) use($busqueda){
                            if ($busqueda != "") {
                                $query->where(function($subquery) use($busqueda){
                                    $subquery->whereRaw("
                                                            CONCAT(
                                                                LPAD(proyectos.id, 4, '0'),
                                                                '-',
                                                                proyectos.forma_ingreso,
                                                                '/',
                                                                IFNULL(
                                                                    a.siglas,
                                                                    ''
                                                                )
                                                            ) = '".$busqueda."'
                                                        ")
                                            ->orWhere("proyectos.id", $busqueda)
                                            ->orWhere("proyectos.nombre", 'like', '%'.$busqueda.'%')
                                            ->orWhere("a.nombre", 'like', '%'.$busqueda.'%')
                                            ->orWhere("a.siglas", 'like', '%'.$busqueda.'%');
                                });
                            }
                        })
                        ->rawColumns(['id', 'acciones'])
                        ->make('true');
    }

    public function create(Request $request){
        $registro   =   new Proyectos;
        $areas      =   Areas::all();
        return view('dashboard.proyectos.agregar', ["registro" => $registro, "areas" => $areas]);
    }

    public function store(Request $request){
        if($request->ajax()){

        	// Agregamos un seo temporal
        	$request->merge(["seo" => "temp"]);

            $response 		= 	BD::crear('Proyectos', $request);

            // Si se guardo bien generamos el seo
            if(($response->status() == 200 || $response->status() == 201) && !$response->getOriginalContent()["error"]){
            	$proyecto 	= 	Proyectos::find($response->getOriginalContent()["id"]);
            	$proyecto->generaSeo();
            }

            return $response;
        }

        return Response::json(["mensaje" => "Petición incorrecta"], 500);
    }

    public function edit(Request $request, $id){
        $registro   =   Proyectos::findOrFail($id);
        $areas      =   Areas::all();
        return view('dashboard.proyectos.agregar', ["registro" => $registro, "areas" => $areas]);
    }

    public function update(Request $request, $id){
        if($request->ajax()){

        	$request->merge(["seo" => "1"]);
            $data   		= 	$request->all();
            $response 		= 	BD::actualiza($id, "Proyectos", $data);

            // Si se guardo bien generamos el seo
            if($response->status() == 200){
            	$proyecto 	= 	Proyectos::find($id);
            	$proyecto->generaSeo();
            }

            return $response;
        }

        return Response::json(["mensaje" => "Petición incorrecta"], 500);
    }

    public function eliminar(Request $request, $id){
        $registro   =   Proyectos::findOrFail($id);
        return view('dashboard.proyectos.eliminar', ["registro" => $registro]);
    }

    public function destroy(Request $request, $id){
        if($request->ajax()){
            return BD::elimina($id, "Proyectos");
        }

        return Response::json(["mensaje" => "Petición incorrecta"], 500);
    }

    public function show($id){
        $proyecto   =    Proyectos::findOrFail($id);
        $titulo     =   "Temporadas de trabajo del proyecto ".$proyecto->nombre;

        return view('dashboard.proyectos.temporadas-trabajo.index', ["titulo" => $titulo, "proyecto" => $proyecto]);
    }

    public function select2(Request $request){
        if($request->ajax()){
            $area_id            =   $request->input('area_id');
            $forma_ingreso      =   $request->input('forma_ingreso');

            $proyectos          =   Proyectos::selectRaw("proyectos.*");

            if($area_id){
                $proyectos      =   $proyectos->where('area_id', $area_id);
            }

            if($forma_ingreso){
                $proyectos      =   $proyectos->where('forma_ingreso', $forma_ingreso);
            }

            $proyectos          =   $proyectos->get();

            $array              =   [];

            $a                  =   [];
            $a["id"]            =   "";
            $a["text"]          =   "";
            array_push($array, $a);

            foreach ($proyectos as $proyecto) {
                $a              =   [];
                $a["id"]        =   $proyecto->id;
                $a["text"]      =   $proyecto->nombre;

                array_push($array, $a);
            }

            return json_encode($array);
        }

        return Response::json(["mensaje" => "Petición incorrecta"], 500);
    }

    public function seeder(){
        if (Auth::user()->rol->nombre == "Administrador") {
            $proyectos          =   Proyectos::all();

            echo "[<br>";

            foreach ($proyectos as $proyecto) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;[<br>";

                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'id' => ".$proyecto->id.",";
                echo "<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'area_id' => ".$proyecto->area_id.",";
                echo "<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'nombre' => '".$proyecto->nombre."',";
                echo "<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'seo' => '".$proyecto->seo."',";
                echo "<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'forma_ingreso' => '".$proyecto->forma_ingreso."',";
                echo "<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'status' => 'Abierto',";
                echo "<br>";

                echo "&nbsp;&nbsp;&nbsp;&nbsp;],<br>";
            }

            echo "<br>]";
        }
    }
}
