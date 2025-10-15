<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Child;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Processor pour fixer le parent sur création (POST).
 */
final class ChildOwnerProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Child) {
            if (null === $data->getParent()) {
                $data->setParent($this->security->getUser());
            }
            $this->em->persist($data);
            $this->em->flush();
        }

        return $data;
    }
}
