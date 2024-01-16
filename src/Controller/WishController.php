<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Service\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/wish', name: 'app_wish')]
class WishController extends AbstractController
{
    #[Route(name: '')]
    public function index(WishRepository $wishRepository): Response
    {
        return $this->render('wish/index.html.twig', [
            'wished' => $wishRepository->findBy([], ["createdAt"=>"DESC"]),
            'user' => $this->getUser()
        ]);
    }


    #[Route('/add', name: '_add')]
    public function addWish(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Censurator $censurator): Response
    {
        $wish = new Wish();
        $wish->setAuthor($this->getUser()->getUserIdentifier());
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $wish->setDescription($censurator->purify($wish->getDescription()));
            /** @var UploadedFile $brochureFile */
            $imageFile = $form->get('imageFileName')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur avec l\'image !');
                }
               $wish->setImageFileName($newFilename);
            }
            $wish = $form->getData();
            $entityManager->persist($wish);
            $entityManager->flush();
            $this->addFlash('success', 'Wish créé !');
            return $this->redirectToRoute('app_wish');
        }

        return $this->render('wish/add.html.twig', [
            'wish' => $form,
        ]);
    }

    #[Route('/details/{id}', name: '_details')]
    public function detail(Wish $wish): Response
    {
        return $this->render('wish/details.html.twig', compact('wish'));
    }

    #[Route('/update/{id}', name: '_update')]
    public function update(Wish $wish, EntityManagerInterface $entityManager, Request $request, Censurator $censurator): Response
    {
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $wish->setDescription($censurator->purify($wish->getDescription()));
            $wish->setUpdatedAt(new \DateTime());
            $entityManager->flush();
            $this->addFlash('success', 'Wish update !');
            return $this->redirectToRoute('app_wish_details', ['id' => $wish->getId()]);
        }

        return $this->render('wish/update.html.twig', [
            'wish' => $form,
        ]);
    }

    #[Route('/delete', name: '_delete')]
    public function delete(Request $request, EntityManagerInterface $em)
    {
        $idWish = $request->request->get("delete-wish");
        $csrfToken = $request->request->get("csrf_token");
        if($request->isMethod("POST") && !empty($idWish) && $this->isCsrfTokenValid("delete_wish_".$idWish, $csrfToken) ){
            $wish = $em->find(Wish::class,$idWish);
            if ($wish->getImageFileName() !== null) {
                unlink($this->getParameter('image_directory')."/".$wish->getImageFileName());
            }
            $em->remove($wish);
            $em->flush();
            $this->addFlash("success","Wish supprimé !");
        }
        return $this->redirectToRoute("app_wish");
    }
}
