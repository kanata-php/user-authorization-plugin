<?php

namespace UserAuthorization\Rules;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UserAuthorization\Rules\RecordDoesntExist;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RecordDoesntExistValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (
            !class_exists($constraint->input['model'])
            || !$constraint instanceof RecordDoesntExist
        ) {
            throw new UnexpectedTypeException($constraint, RecordDoesntExist::class);
        }

        if ($constraint->input['model']::where($constraint->input['field'], $value)->exists()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ model }}', $value)
                ->addViolation();
        }
    }
}