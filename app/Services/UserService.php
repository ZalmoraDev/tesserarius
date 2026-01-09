<?php

namespace App\Services;

use App\Repositories\UserBaseRepository;

final class UserService
{
    private UserBaseRepository $userRepository;

    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
