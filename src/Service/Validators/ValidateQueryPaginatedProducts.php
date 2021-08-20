<?php


namespace App\Service\Validators;


use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints as Assert;

class ValidateQueryPaginatedProducts
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function __invoke($filter, $page, $limit, $order_by, $order_dir)
    {
        $input = [
            'filter' => $filter,
            'page' => $page,
            'limit' => $limit,
            'order_by' => $order_by,
            'order_dir' => $order_dir,
        ];

        $constraint = new Assert\Collection([
            'filter' => new Assert\Type(['type' => 'string']),
            'page' => [
                // new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'limit' => [
                // new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'order_by' => new Assert\Choice(['id', 'name', 'price']),
            'order_dir' => new Assert\Choice(['ASC', 'DESC']),
        ]);

        $errors = $this->validator->validate($input, $constraint);
        // var_dump($errors);

        return $errors;
    }
}