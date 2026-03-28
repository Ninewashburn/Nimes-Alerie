<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeFormType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TypeController extends AbstractController
{
    private PaginatorInterface $paginator;
    private TypeRepository $typeRepository;
    private EntityManagerInterface $em;

    /**
     * @param PaginatorInterface $paginator
     * @param TypeRepository $typeRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PaginatorInterface $paginator, TypeRepository $typeRepository, EntityManagerInterface $em)
    {
        $this->paginator = $paginator;
        $this->typeRepository = $typeRepository;
        $this->em = $em;
    }


    #[Route('/type', name: 'type_index')]
    public function indexType(Request $request): Response
    {
        $queryBuilder = $this->typeRepository->getQbAll();
        $typesList = $this->paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 3);

        return $this->render('type/index.html.twig', [
            'controller_name' => 'TypeController',
            'types' => $typesList,
        ]);
    }

    #[Route('/add', name: 'type_add')]
    public function addType(Request $request): Response
    {
        $typeEntity = new Type ();
        $form = $this->createForm(TypeFormType::class, $typeEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($typeEntity);
            $this->em->flush();
            return $this->redirectToRoute('type_index');
        }

        return $this->render('type/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'type_view')]
    public function viewType(int $id): Response
    {

        return $this->render('type/view.html.twig', [
            'typeView' => $this->typeRepository->find($id),
        ]);
    }

}
