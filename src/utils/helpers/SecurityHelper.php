<?php

namespace WebComplete\core\utils\helpers;

class SecurityHelper
{

    /**
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public function cryptPassword(string $password, string $salt): string
    {
        return \strtoupper(\md5(\md5($salt) . \md5($password)));
    }
}
