<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Exception utilisée dans CheckVerifiedUserSubscriber
 */
class AccountNotVerifiedAuthenticationException extends AuthenticationException
{
}