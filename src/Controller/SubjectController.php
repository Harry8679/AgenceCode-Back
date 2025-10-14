<?php

namespace App\Controller;

use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted as AttributeIsGranted;

#[Route('/api/subjects')]
final class SubjectController extends AbstractController
{
    #[Route('', name: 'subject_create', methods: ['POST'])]
    #[AttributeIsGranted('ROLE_ADMIN')] // <- double sécurité côté contrôleur
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $name = trim((string)($data['name'] ?? ''));

        if ($name === '') {
            return new JsonResponse(['message' => 'name est requis'], Response::HTTP_BAD_REQUEST);
        }

        // unicité
        $repo = $em->getRepository(Subject::class);
        if ($repo->findOneBy(['name' => $name])) {
            return new JsonResponse(['message' => 'Cette matière existe déjà'], Response::HTTP_CONFLICT);
        }

        $slugger = new AsciiSlugger();
        $slug = strtolower($slugger->slug($name));

        // unique également sur slug
        if ($repo->findOneBy(['slug' => $slug])) {
            return new JsonResponse(['message' => 'Slug déjà utilisé'], Response::HTTP_CONFLICT);
        }

        $subject = (new Subject())
            ->setName($name)
            ->setSlug($slug);

        $em->persist($subject);
        $em->flush();

        return new JsonResponse([
            'id'   => $subject->getId(),
            'name' => $subject->getName(),
            'slug' => $subject->getSlug(),
        ], Response::HTTP_CREATED);
    }
}