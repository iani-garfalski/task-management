<?php
namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_categories()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Hash the password
        ]);

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Create a category
        Category::factory()->create(['name' => 'Work']);

        // Fetch the categories from the API with the token in the Authorization header
        $response = $this->getJson('/api/categories', [
            'Authorization' => 'Bearer ' . $token, // Pass the token in the Authorization header
        ]);

        // Assert that the response is successful and contains the correct structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name'] // Adjusted structure to reflect wrapping in 'data'
                     ]
                 ]);
    }

    public function test_can_create_category()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Hash the password
        ]);

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Send a POST request to create a category with the token in the Authorization header
        $response = $this->postJson('/api/categories', ['name' => 'Personal'], [
            'Authorization' => 'Bearer ' . $token, // Pass the token in the Authorization header
        ]);

        // Assert that the category was created and return the correct status and data
        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'name' => 'Personal', // The returned data should include the 'data' wrapper
                         'id' => true, // Check if 'id' exists
                     ]
                 ]);
    }
}
