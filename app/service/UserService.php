<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
}
