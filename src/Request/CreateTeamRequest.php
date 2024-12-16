<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTeamRequest extends AbstractJsonRequest
{
    #[Assert\NotBlank]
    public readonly string $name;

    #[Assert\NotBlank]
    public readonly string $city;

    // TODO: replace min and max with const, ensure that yearFounded cannot be in the future, find the way to dynamically calculate the max year to be current year
    #[Assert\NotBlank]
    #[Assert\Range(min: 1850, max: 2100)]
    public readonly int $yearFounded;

    #[Assert\NotBlank]
    public readonly string $stadiumName;
}
