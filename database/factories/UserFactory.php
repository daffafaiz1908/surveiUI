<?php

namespace Database\Factories;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Shared faker instance.
     */
    protected static $fakerInstance = null;

    /**
     * Get the faker instance.
     */
    protected function getFaker()
    {
        if (static::$fakerInstance === null) {
            static::$fakerInstance = FakerFactory::create('id_ID');
        }
        return static::$fakerInstance;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Gunakan $this->faker jika tersedia, jika tidak gunakan instance yang di-share
        $faker = $this->faker ?? $this->getFaker();
        
        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Password default: 'password'
            'remember_token' => Str::random(10),
            // Secara default, role-nya adalah mahasiswa
            'role' => 'mahasiswa',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * State baru untuk role Dosen
     */
    public function dosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'dosen',
        ]);
    }

    /**
     * State baru untuk role Staff
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'staff',
        ]);
    }
}