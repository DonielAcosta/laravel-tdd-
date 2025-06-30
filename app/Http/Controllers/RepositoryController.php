<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use App\http\Requests\RepositoryRequest;

class RepositoryController extends Controller{

  public function index(Request $request)
  {
      $user = $request->user();
  
      // Si no hay usuario logueado, retorna colección vacía
      $repositories = $user ? $user->repositories : collect();
  
      return view('repositories.index', compact('repositories'));
  }
  
  public function show(Request $request, Repository $repository){
      if ($request->user()->id != $repository->user_id) {
          abort(403);
      }

      return view('repositories.show', compact('repository'));
  }

  public function edit(Request $request, Repository $repository){
    if ($request->user()->id != $repository->user_id) {
        abort(403);
    }

    return view('repositories.edit', compact('repository'));
  }

  public function create(){
    return view('repositories.create');
  }

  public function store(RepositoryRequest $request){


    $request->user()->repositories()->create($request->all());

    return redirect()->route('repositories.index');
  }

  public function update(RepositoryRequest $request, Repository $repository){


    $repository->update($request->all());
    if($request->user()->id != $repository->user_id){
      abort(403);
    }
    return redirect()->route('repositories.edit', $repository);
  }

  public function destroy(Request $request, Repository $repository){

    $repository->delete();
    if($request->user()->id != $repository->user_id){
      abort(403);
    }
    return redirect()->route('repositories.index');
  }
}