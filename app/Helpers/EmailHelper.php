<?php

namespace App\Helpers;

class EmailHelper
{
    /**
     * Validate an email address.
     *
     * @param string|null $email
     * @return bool
     */
    public static function isValidEmail(?string $email): bool
    {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
