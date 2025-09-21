<?php

namespace App\Dto;

use App\Enum\UserProfile;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [UserProfile::PARENT->value, UserProfile::STUDENT->value, UserProfile::TEACHER->value])]
    public ?string $profile = null;
}