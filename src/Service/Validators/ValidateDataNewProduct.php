<?php


namespace App\Service\Validators;

use App\Entity\Tax;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints as Assert;

class ValidateDataNewProduct
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function __invoke($data, $taxList)
    {
        $taxChoices = [];

        /** @var Tax $tax */
        foreach ($taxList as $tax) {
            $taxChoices[] = $tax->getId();
        }

        $constraint = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'description' => [
                new Assert\Type(['type' => 'string']),
            ],
            'price' => [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank(),
                new Assert\Positive(),
            ],
            'tax' => new Assert\Choice($taxChoices),
        ]);

        $errors = $this->validator->validate($data, $constraint);

        return $errors;
    }
}