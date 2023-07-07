<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Service;
use App\Models\{ Extension, Inscription };
use PDF;
use Illuminate\Support\Facades\Storage;

class ExtensionController extends Controller
{

    private $service;
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'valid_until_at' => ['required', 'date', 'after:today'],
            'student_id' =>  ['required', 'exists:students,id']
        ]);

        //get current academy year and found if student has registered
        $current_academy = $this->service->currentAcademy($request);
        
        if(!$current_academy) return response()->json([
             "errors" => [
                "message" => "Aucune annee academique active."
             ]
        ], 422);

        $registration = Inscription::where([
            ["academy_id", $current_academy->id],
            ["student_id", $request->student_id]
        ])->first();

        if(!$registration) return response()->json([
            "errors" => [
                "message" => "Cet eleve n'est pas inscrit pour l'annee en cours."
            ]
        ], 422);

        $extension = Extension::create([
            'valid_until_at' => $request->valid_until_at,
            'inscription_id' => $registration->id
        ]);

        return $extension;

        //generate pdf file before check if folder exists or not
       /*  $user = $request->user();
        $pdf = PDF::loadView('pdf.index', compact('extension', 'user')); */
       /*  return $pdf->output();
                
        $path = "app/public/extended";

        if(!Storage::exists($path)){
            Storage::makeDirectory($path);
        }

        //save folder to path
        $file_name = time()."-".$extension->inscription->student_id.".pdf";
        $pdf->save(storage_path($path."/".$file_name)); */
        
        //Storage::download('file.jpg', $name, $headers);
        //return storage_path($path.'/'.$file_name);
        //return response()->download(storage_path($path.'/'.$file_name));
        return response()->noContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $extension = Extension::find($id);

        if($extension) return response()->json([
            "errors" => [
                "message" => "Ce moratoire n'existe pas"
            ] 
        ]);

        $extension->delete();

        return response()->noContent();
    }

    public function download(Request $request, $id) {

        $user = $request->user();
        $signature_base64 = public_path('storage/signatures/').$user->accounts[0]->signature_base64;
        $extension = Extension::findOrFail($id);
        $pdf = PDF::loadView('pdf.index', compact('extension', 'user', 'signature_base64'));
        return $pdf->output();

    }
}
