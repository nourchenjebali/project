<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\MinMaxType;
use App\Form\RechercheType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/showbook', name: 'showbook')]
    public function showbook(BookRepository $bookRepository,Request $req): Response
    {
        $book =$bookRepository->findAll();
        $form=$this->createForm(RechercheType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()){
        $datain=$form->get('ref')->getData();
        $book=$bookRepository->rechercheminmax($datain);
        //var_dump($datain).die();
        }
        return $this->renderForm('book/showbook.html.twig', [
            'book' => $book,
            'f'=>$form
        ]);
    }
   
    #[Route('/addbook', name: 'addbook')]
    public function addbook(ManagerRegistry $managerRegistry,Request $req): Response
    {
        $em= $managerRegistry->getManager();
        $book= new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()){
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('showbook');
        }
        return $this->renderForm('book/addbook.html.twig', [
            'f' => $form
        ]);
    }
    #[Route('/editbook/{id}', name: 'editbook')]
    public function editbook($id,ManagerRegistry $managerRegistry,Request $req,BookRepository $bookRepository): Response
    {
        $em= $managerRegistry->getManager();
        $dataid= $bookRepository->find($id);
        $form=$this->createForm(BookType::class,$dataid);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()){
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showbook');
        }
        return $this->renderForm('book/editbook.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/deletebook/{id}', name: 'deletebook')]
    public function deletebook($id, ManagerRegistry $managerRegistry, BookRepository $repo): Response
    {
        $em = $managerRegistry->getManager();
        $id = $repo->find($id);
        $em->remove($id);
        $em->flush();
        return $this->redirectToRoute('showbook');
    }
}
