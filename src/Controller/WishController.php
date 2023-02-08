<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\UserRepository;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wish', name: 'wish')]
class WishController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function list(
        WishRepository $wishRepository
    ): Response
    {
        $wishList = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);
        return $this->render('wish/list.html.twig', [
            'wishList' => $wishList,
        ]);
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function detail(
        int            $id,
        WishRepository $wishRepository
    ): Response
    {
        $wish = $wishRepository->findOneBy(['id' => $id]);
        return $this->render('wish/detail.html.twig', [
            'wish' => $wish
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/create', name: '_create')]
    public function create(
        EntityManagerInterface $em,
        Request                $request,
        UserRepository         $userRepository
    ): Response
    {
        $wish = new Wish();

        // ajout de l'auteur pour affichage à la création du wish
        $utilisateur = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $wish->setAuthor($utilisateur->getUsername());
        // OU $wish->setAuthor($this->getUser()->getUserIdentifier());

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted()) {
            try {
                $wish->setDateCreated(new \DateTime());
                $wish->setIsPublished(true);

                if ($wishForm->isValid()) {
                    $em->persist($wish);
                }
            } catch (Exception $e) {
                dd($e->getMessage());
            }
            $em->flush();
            $this->addFlash('Submit succeed', 'Wish successfully added !');
            return $this->redirectToRoute('wish_list');
        }

        return $this->render('wish/create.html.twig',
            [
                'wishForm' => $wishForm->createView()
            ]);
    }
}
