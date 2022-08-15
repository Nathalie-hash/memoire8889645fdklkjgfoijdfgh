<?php

namespace App\Http\Controllers;

use App\User;
use App\Niveau;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

// Gestion des rôles des utilisateurs
class RoleController extends Controller
{
	// revue des rôles et niveaux auquels appartiennent les utilisateurs de l'application
	public function index(){
		$users = User::all();
		return view('role.index', compact('users'));
	}


    // pour assigner les rôles et les niveaux aux utilisateurs
	public function config(Request $request){
		$users = User::all();
		$roleNames = ['administrateur','enseignant', 'etudiant'];
		$rolesModels = array();
		foreach($roleNames as $roleName){
			$rolesModels[] = Role::firstOrCreate([
				"name" => $roleName
			]);
		}
		$niveaux = Niveau::all();
		return view('role.config', compact(['users','rolesModels','niveaux']));
	}

	// édition de rôle d'un utilisateur
	public function save(Request $request){

		// TODO: quand on a déjà un utilisateur de rôle admin et que 
		// l'on connaît ses identifiants de connexion, 
		// seul l'admin peut effectuer cet action
		

		$user = User::find($request->user);
		$role = Role::find($request->role);

		// la liste des id des niveaux auquels appartient l'utilisateur
		$niveauIdsUser = $user->niveaux->reduce(function($carry,$item){
			$carry[] = $item->id;
			return $carry;
		},[]);

		// liste des ids des niveaux auquels on veut que l'utilisateurs appartienne
		$niveauxIds = is_array($request->niveau) ? $request->niveau : [];

		// on associe l'utilisateur aux niveaux auquels il veut appartenir
		foreach($niveauxIds as $niveauId){
			$niveauSave = $user->niveaux->where('id',$niveauId)->first();
			// s'il n'appartient pas à ce niveau, on fait l'association
			if(is_null($niveauSave)){
				$user->niveaux()->save(Niveau::find($niveauId));
			}
		}

		// liste des ids des niveaux auquels l'utilisateurs ne vas plus appartenir
		$niveauxIdsOublier = array_diff($niveauIdsUser, $niveauxIds);
		foreach($niveauxIdsOublier as $niveauId){
			$niveauDelete = Niveau::find($niveauId);
			$niveauDelete->delete();
		}
		
		// on assignge ce role à l'utilisateur
		// pour assigner manuellement, regarder dans la table model_has_roles
		$user->assignRole($role->name);
	}


}
