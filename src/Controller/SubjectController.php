<?php

namespace App\Controller;

use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/api/subjects', name: 'api_subject_')]
final class SubjectController extends AbstractController
{
    /**
     * Liste des matières (ouvert, utile pour l’autocomplete côté front)
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $subjects = $em->getRepository(Subject::class)->findBy([], ['name' => 'ASC']);

        return $this->json(array_map(
            fn (Subject $s) => [
                'id'   => $s->getId(),
                'name' => $s->getName(),
                'slug' => $s->getSlug(),
            ],
            $subjects
        ));
    }

    /**
     * Création d’une matière — réservé aux admins
     */
    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $name = trim((string)($data['name'] ?? ''));

        if ($name === '') {
            return new JsonResponse(['message' => 'Le champ "name" est requis.'], Response::HTTP_BAD_REQUEST);
        }

        $repo = $em->getRepository(Subject::class);
        if ($repo->findOneBy(['name' => $name])) {
            return new JsonResponse(['message' => 'Cette matière existe déjà.'], Response::HTTP_CONFLICT);
        }

        $slugger = new AsciiSlugger();
        $slug = strtolower($slugger->slug($name));

        if ($repo->findOneBy(['slug' => $slug])) {
            return new JsonResponse(['message' => 'Slug déjà utilisé.'], Response::HTTP_CONFLICT);
        }

        $subject = (new Subject())
            ->setName($name)
            ->setSlug($slug);

        $em->persist($subject);
        $em->flush();

        return $this->json([
            'id'   => $subject->getId(),
            'name' => $subject->getName(),
            'slug' => $subject->getSlug(),
        ], Response::HTTP_CREATED);
    }
}