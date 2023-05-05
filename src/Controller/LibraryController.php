<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;

class LibraryController extends AbstractController
{
    #[Route("/library", name:"library")]
    public function libstart(): Response
    {
        return $this->render('library.html.twig');
    }

    #[Route("/allBooks", name:"allBooks", methods: ['GET'])]
    public function library(BookRepository $booksRepository): Response
    {
        $data = [
            "books" => $booksRepository->findAll()
        ];
        return $this->render('allBooks.html.twig', $data);
    }

    #[Route("/allBooks", name:"allBooksPost", methods: ['POST'])]
    public function libraryPost(Request $request, ManagerRegistry $doctrine): Response
    {
        $bookId = $request->request->get("id");
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($bookId);
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('allBooks');
    }

    #[Route("/createBook", name:"createBook", methods: ['GET'])]
    public function createBook(): Response
    {
        return $this->render('createBook.html.twig');
    }

    #[Route("/createBook", name:"createBookPost", methods: ['POST'])]
    public function createBookPost(Request $request, ManagerRegistry $doctrine): Response
    {
        $isbn = $request->request->get('ISBN');
        $title = $request->request->get('title');
        $author = $request->request->get('author');
        $img = $request->request->get('img');
        $entityManager = $doctrine->getManager();
        $book = new Book();
        $book->setISBN($isbn);
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setImage($img);
        $entityManager->persist($book);
        $entityManager->flush();
        return $this->redirectToRoute('allBooks');
    }


    #[Route('/book/show/{bookId}', name: 'book_by_id', methods: ['GET'])]
    public function showBookById(int $bookId,BookRepository $booksRepository): Response
    {
        $data = [
            "book" => $booksRepository->find($bookId)
        ];
        return $this->render('oneBook.html.twig', $data);
    }

    #[Route('/book/update/{bookId}', name: 'updateBook', methods: ['GET'])]
    public function updateBook(int $bookId,BookRepository $booksRepository): Response
    {
        $book = $booksRepository->find($bookId);
        if (!$book) {
            return $this->redirectToRoute('allBooks');
        }
        $data = [
            "book" => $book
        ];
        return $this->render('updateBook.html.twig', $data);
    }

    #[Route('/book/update/{bookId}', name: 'updateBookPost', methods: ['POST'])]
    public function updateBookPost(int $bookId,Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($bookId);
        $isbn = $request->request->get('ISBN');
        $title = $request->request->get('title');
        $author = $request->request->get('author');
        $img = $request->request->get('img');
        $book->setISBN($isbn);
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setImage($img);
        $entityManager->flush();
        return $this->redirectToRoute('allBooks');
    }
}
