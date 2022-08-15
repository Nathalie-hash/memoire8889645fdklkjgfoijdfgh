<?php

namespace App\Http\Controllers;

use App\Niveau;
use App\Matiere;
use App\Ressource;
use App\User;
use Illuminate\Http\Request;

class RessourcesController extends Controller
{
    // supprimer un ressource
    public function delete($id){
        $resource = Ressource::find($id);
        $matiere = $resource->matiere;
        $resource->delete();
        return Redirect::back();
    }

    // pour pouvoir télécharger une ressource
    public function getRessource($id){ 
    // TODO: seul les personnes connectés ayant un rôle
    // étudiant ou enseignant ou administrateur peuventa accéder au ressource
    // TODO: si le rôle est étudiant et qu'il n'est pas dans
    // le niveau où se trouve cette ressource, refuser l'accès
    // Pour rappel on a
    // user => niveau => matiere => ressource
    $matiere = Ressource::where('id',$id)->get()->first()["matiere_id"];
    $niveau_matiere = Matiere::where('id',$matiere)->get()->first()["niveau_id"];
    $id_user=auth()->user()->id;
    $niveau_user= auth()->user()->niveaux()->where('user_id',$id_user)->get()->first()["pivot"]["niveau_id"];
    if (auth()->check() && auth()->user()->hasRole('enseignant')){
        $resource = Ressource::find($id);
    }else if (auth()->check() && auth()->user()->hasRole('administrateur')) {
        $resource = Ressource::find($id);
    }else if(auth()->check() && auth()->user()->hasRole('etudiant') && $niveau_matiere == $niveau_user)  {
        $resource = Ressource::find($id);   
    }else{
        abort(401, 'You are not allowed to access this page');
    }

    // TODO: regarder le tuto et envoyer les fichiers
    // les informations dans $ressource doivent permettre d'envoyer le fichier


}
        
    
   

    // pour ajouter une ressource
    public function add(Request $request){
        dump($request->file('ressource'));
        dump($request->file('ressource')->getClientOriginalName());

        $path = $request->file('ressource')->store('avatars');
        dump($request->matiere);
        dump($path);
        // die();
        // TODO: vérifier les rôles car seul les admin et les enseignants
        // peuvent ajouter des ressources à une matière
        // TODO: si pas admin ou enseignant
        // renvoyé un erreur 404 avec Laravel
        echo $request->ressource;
        // if (auth()->check() && auth()->user()->hasRole('enseignant')||auth()->check() && auth()->user()->hasRole('administrateur')) {
            

        // }else{
        //     abort(404);
        // }
       
        // stocker le fichier dans le disk correspondant
        // on va faire un disque par matière

        // TODO: ne pas stocker le fichier dans un disque accéssible au public
        $matiere = Matiere::find($request->matiere);
        dump($matiere);

        $niveau = $matiere->niveau;
        dump($niveau);

        // TODO: enregistrer les informations sur la ressource
        // de sorte que l'on puisse la retrouver dans resources/views/matieres/show.blade.php
        $resource = new Ressource();
        $resource->chemin = $path;
        $resource->matiere_id = $request->matiere;
        $resource->nom = $request->file('ressource')->getClientOriginalName();
        $resource->save();



        // TODO: lister les étudiants de cette matière et leur notifier
        // qu'une ressource vient d'être mise en ligne

        // TODO: on redirige vers la page de détail de la matière

    }
}
