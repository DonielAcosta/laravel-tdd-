<?php


namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Repository;

class RepositoryControllerTest extends TestCase{

  use WithFaker, RefreshDatabase;

  public function test_guest(){

    $this->get('repositories')->assertRedirect('login');        // index
    $this->get('repositories/1')->assertRedirect('login');      // show
    $this->get('repositories/1/edit')->assertRedirect('login'); // edit
    $this->put('repositories/1')->assertRedirect('login');      // update
    $this->delete('repositories/1')->assertRedirect('login');   // destroy
    $this->get('repositories/create')->assertRedirect('login'); // create
    $this->post('repositories', [])->assertRedirect('login');   // store
  }


  public function test_index_empty(){

      Repository::factory()->create(); // user_id = 1
    /** @var \App\Models\User $user */
      $user = User::factory()->create(); // id = 2

      $this
          ->actingAs($user)
          ->get('repositories')
          ->assertStatus(200)
          ->assertSee('No hay repositorios creados');
  }

  public function test_index_whit_data(){
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
   $repository = Repository::factory()->create(['user_id'=> $user->id]);

    $this->actingAs($user)
    ->get('repositories')
    ->assertStatus(200)
    ->assertSee($repository->id)
    ->assertSee($repository->url);

  }
  public function test_create(){
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $this->actingAs($user)
    ->get('repositories/create')
    ->assertStatus(200);

  }
  public function test_store()
  {
      $data = [
          'url' => $this->faker->url,
          'description' => $this->faker->text,
      ];

      /** @var \App\Models\User $user */
      $user = User::factory()->create();

      $this
          ->actingAs($user)
          ->post('repositories', $data)
          ->assertRedirect('repositories');

      $this->assertDatabaseHas('repositories', $data);
  }

  public function test_update()
  {
      /** @var \App\Models\User $user */
      $user = User::factory()->create();

      $repository = Repository::factory()->create([
          'user_id' => $user->id
      ]);

      $data = [
          'url' => $this->faker->url,
          'description' => $this->faker->text,
      ];

      $this
          ->actingAs($user)
          ->put("repositories/$repository->id", $data)
          ->assertRedirect("repositories/$repository->id/edit");

      $this->assertDatabaseHas('repositories', $data);
  }

  public function test_update_policy(){
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $repository = Repository::factory()->create();
    $data = [
        'url' => $this->faker->url,
        'description' => $this->faker->text,
    ];

    $this
        ->actingAs($user)
        ->put("repositories/$repository->id", $data)
        ->assertStatus(403);

    $this->assertDatabaseHas('repositories', $data);
  }

  public function test_validate_store(){
     /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post('repositories', [])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url', 'description']);
  }

  public function test_validate_update(){

    $repository = Repository::factory()->create();
     /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->put("repositories/$repository->id", [])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url', 'description']);
  }

  public function test_destroy(){
     /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $repository = Repository::factory()->create(['user_id'=> $user->id]);

    $this
        ->actingAs($user)
        ->delete("repositories/$repository->id")
        ->assertRedirect('repositories');

    $this->assertDatabaseMissing('repositories', [
        'id' => $repository->id,
        'url' => $repository->url,
        'description' => $repository->description,
    ]);
  }

  public function test_destroy_policy(){

    $user = User::factory()->create();
    /** @var \App\Models\User $user */
    $repository = Repository::factory()->create();
    $this
        ->actingAs($user)
        ->delete("repositories/$repository->id")
        ->assertStatus(403);
  }
  public function test_show(){
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $repository = Repository::factory()->create(['user_id' => $user->id]);

    $this
        ->actingAs($user)
        ->get("repositories/$repository->id")
        ->assertStatus(200);
  }

  public function test_show_policy(){
    /** @var \App\Models\User $user */
    $user = User::factory()->create(); // id = 1
    $repository = Repository::factory()->create(); // user_id = 2

    $this
        ->actingAs($user)
        ->get("repositories/$repository->id")
        ->assertStatus(403);
  }

  public function test_edit(){
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $repository =Repository::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
    ->get("repositories/$repository->id/edit")
    ->assertStatus(200)
    ->assertSee($repository->url)
    ->assertSee($repository->description);


  }

  public function test_edit_policy(){
       /** @var \App\Models\User $user */
       $user = User::factory()->create(); // id = 1
       $repository = Repository::factory()->create(); // user_id = 2
   
       $this
           ->actingAs($user)
           ->get("repositories/$repository->id/edit")
           ->assertStatus(403);
  }


}