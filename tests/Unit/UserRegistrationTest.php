<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    /**
     * Test that a user can be registered with proper attributes
     */
    public function testUserCanRegister(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];
        
        // Create a new user
        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->save();
        
        // Assert the user exists in the database
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name']
        ]);
        
        // Assert the password was hashed correctly
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        // Clean up
        $user->delete();
    }
}
