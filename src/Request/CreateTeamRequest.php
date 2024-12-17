<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTeamRequest extends AbstractJsonRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The name must be at least {{ limit }} characters long.',
        maxMessage: 'The name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The name must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly string $name;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The city must be at least {{ limit }} characters long.',
        maxMessage: 'The city cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The city must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly string $city;

    // TODO: replace min and max with const, ensure that yearFounded cannot be in the future, find the way to dynamically calculate the max year to be current year
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 4,
        max: 4,
        exactMessage: 'The year must be exactly {{ limit }} digits long.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{4}$/',
        message: 'The year must contain only numbers.'
    )]
    #[Assert\Range(
        notInRangeMessage: 'The year must be between {{ min }} and {{ max }}.',
        min: 1850,
        max: 2024
    )]
    public readonly string $yearFounded;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The stadium name must be at least {{ limit }} characters long.',
        maxMessage: 'The stadium name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The stadium name must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly string $stadiumName;
}
