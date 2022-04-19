<?php

declare(strict_types=1);

namespace Future\Blog\User\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RegistrationEmail extends Constraint
{
    public string $message = 'The E-mail {{ string }} already exists!!!';
}
