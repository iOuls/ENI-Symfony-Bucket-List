<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

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

    #[Route('/create', name: '_create')]
    public function create(
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        $wish = new Wish();
        $wish->setDateCreated(new \DateTime());
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $em->persist($wish);
            $em->flush();

            $this->addFlash('Ajout effectué', 'Votre souhait a été ajouté avec succès');

            return $this->redirectToRoute('wish_list');
        }

        return $this->render('wish/create.html.twig',
            [
                'wishForm' => $wishForm->createView()
            ]);
    }
}
