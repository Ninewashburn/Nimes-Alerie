<?php

namespace App\Controller;

use App\Entity\SubType;
use App\Form\SubTypeForm;
use App\Repository\SubTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subtypes')]
class SubTypeController extends AbstractController
{
    private SubTypeRepository $subTypeRepository;
    private EntityManagerInterface $em;
    private PaginatorInterface $paginator;

    /**
     * @param SubTypeRepository $subTypeRepository
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     */
    public function __construct(SubTypeRepository $subTypeRepository, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->subTypeRepository = $subTypeRepository;
        $this->em = $em;
        $this->paginator = $paginator;
    }


    #[Route('/add', name: 'subtype_add')]
    public function addSubtype(Request $request): Response
    {
        $subtypeEntity = new SubType ();
        $form = $this->createForm(SubTypeForm::class, $subtypeEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($subtypeEntity);
            $this->em->flush();
            return $this->redirectToRoute('type_view', ['id' => $subtypeEntity->getType()->getId()]);
        }

        return $this->render('subtype/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'subtype_view')]
    public function viewSubtype(int $id): Response
    {

        return $this->render('subtype/view.html.twig', [
            'subtypeView' => $this->subTypeRepository->find($id),
        ]);
    }
}
