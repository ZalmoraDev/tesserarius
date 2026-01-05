<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
