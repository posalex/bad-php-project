<?php

namespace App\Controller;

use Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{
    public $hostname = '127.0.0.1';
    public $username = 'admin';
    public $password = 'solarwinds123';
    public $database = 'books';

    #[Route('/book', name: 'app_book_all')]
    public function all(): JsonResponse
    {
        $db = new Database($this->hostname, $this->username, $this->password, $this->database);

        $data = $db->query('SELECT id, title, author, price FROM books');

        return $this->json($data);
    }

    #[Route('/book/{id}', name: 'app_book_get')]
    public function get($id): JsonResponse
    {
        $db = new Database($this->hostname, $this->username, $this->password, $this->database);

        $data = $db->query('SELECT id, title, author, price FROM books WHERE id = ' . $id);

        return $this->json($data);
    }

    #[Route('/books/create', name: 'app_book_create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        $db = new Database($this->hostname, $this->username, $this->password, $this->database);

        $data = $db->query('INSERT INTO books (id, title, author, price) FROM books WHERE id = ' . $id);

        return $this->json($data);
    }
}
