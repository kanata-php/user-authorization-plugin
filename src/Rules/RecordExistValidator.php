<?php

namespace UserAuthorization\Rules;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UserAuthorization\Rules\RecordExist;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RecordExistValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (
            !class_exists($constraint->input['model'])
            || !$constraint instanceof RecordExist
        ) {
            throw new UnexpectedTypeException($constraint, RecordExist::class);
        }

        if (!$constraint->input['model']::where($constraint->input['field'], $value)->exists()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ model }}', (string) $value)
                ->addViolation();
        }
    }
}