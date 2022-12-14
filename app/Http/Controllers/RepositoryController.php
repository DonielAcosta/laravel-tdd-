<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;

class RepositoryController extends Controller{

  public function index(Request $request){
    return view('repositories.index', [
      'repositories' => $request->user()->repositories
    ]);
  }
  public function show(Request $request, Repository $repository){
      if ($request->user()->id != $repository->user_id) {
          abort(403);
      }

      return view('repositories.show', compact('repository'));
  }
  public function store(Request $request){
    
    $request->validate([
        'url' => 'required',
        'description' => 'required',
    ]);

    $request->user()->repositories()->create($request->all());

    return redirect()->route('repositories.index');
  }

  public function update(Request $request, Repository $repository){

    $request->validate([
        'url' => 'required',
        'description' => 'required',
    ]);
    
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