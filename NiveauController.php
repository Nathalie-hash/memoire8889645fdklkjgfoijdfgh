<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Niveau;

class NiveauController extends Controller
{
    //
    public function index(){

        return view('niveaus.index');
    }

    public function show($id){
        $niveau = Niveau::with('matieres')->where('id',$id)->get()->first();
        return view('niveaus.show', compact('niveau'));
        
    }
}
