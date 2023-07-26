<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{ Inscription, Transaction, Student };
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "lname" => $this->faker->name,
            "fname" => $this->faker->lastName,
            "matricule" => $this->faker->randomNumber(5, true),
            "slug" => Str::slug($this->faker->name.' '.$this->faker->name.' '.$this->faker->randomNumber(5, true), '-'),
            "sexe" => $this->faker->randomKey(['M' => 1, 'F' => 2]),
            "father_name" => $this->faker->name,
            "mother_name" => $this->faker->name,
            "fphone" => $this->faker->e164PhoneNumber(),
            "mphone" => $this->faker->e164PhoneNumber(),
            "born_at" => $this->faker->date(),
            "born_place" => $this->faker->text,
            "allergy" => $this->faker->text,
            "description" => $this->faker->text,
            "quarter" => $this->faker->text,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Student $student) {
            
            $inscription = \App\Models\Inscription::create([
                "classroom_id" => 2,
                "academy_id" => 1,
                "student_id" => $student->id
            ]);

            \App\Models\Transaction::create([
                "inscription_id" => $inscription->id,
                "amount" => 15000,
                "name" => "inscription"
            ]);
        });
    }
}
