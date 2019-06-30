<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Arr;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegistration()
    {
        $payload = ['user_name' => 'testlogin@user.com', 'password' => 'toptal123'];

        $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'user_id',
                'user_name',
                'credentials',
            ]);

        $this->assertDatabaseHas('users', Arr::only($payload, ['user_name']));
    }

    public function testLogin()
    {
        factory(User::class)->create([
            'user_name' => 'testlogin@user.com',
            'password' => bcrypt('toptal123'),
        ]);

        $payload = ['user_name' => 'testlogin@user.com', 'password' => 'toptal123'];

        $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'user_id',
                'user_name',
                'credentials',
            ]);

        $this->assertDatabaseHas('users', Arr::only($payload, ['user_name']));
    }

    public function testShow()
    {
        $this->login();

        $user = factory(User::class)->create([
            'user_name' => 'testlogin@user.com',
            'password' => bcrypt('toptal123'),
        ]);

        $this->json('GET', "api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'user_name' => $user->user_name,
            ]);
    }

    public function testIndex()
    {
        $usersCount = 5;
        $users = factory(User::class, $usersCount)->create();
        $this->login($users[0]);


        $this->json('GET', "api/users")
            ->assertStatus(200)
            ->assertJsonStructure(["*" => [
                'user_id',
                'user_name',
            ]])
            ->assertJsonCount($usersCount);
    }
}
