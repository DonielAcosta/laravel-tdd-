<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepositoryFactory extends Factory{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Repository::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition(){

      return [
          'user_id' => User::factory(),
          'url' => $this->faker->url,
          'description' => $this->faker->text,
      ];
  }
  public function test_update(){

    $repository = Repository::factory()->create();
    $data = [
        'url' => $this->faker->url,
        'description' => $this->faker->text,
    ];

    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->put("repositories/$repository->id", $data)
        ->assertRedirect("repositories/$repository->id/edit");

    $this->assertDatabaseHas('repositories', $data);
  }
}