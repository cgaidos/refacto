<?php

class UserRepository implements Repository
{
    use SingletonTrait;

    private $id;
    private $firstname;
    private $lastname;
    private $email;

    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $faker = \Faker\Factory::create();
        $this->id = $faker->randomNumber();
        $this->firstname = $faker->firstname;
        $this->lastname = $faker->lastname;
        $this->email = $faker->email;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function getById($id)
    {
        return new User (
            $id,
            $this->firstname,
            $this->lastname,
            $this->email
        );
    }
}
