<?php

namespace App\Controller;

use App\Export\CSVExporter;
use App\Export\ExcelExporter;
use App\Export\JSONExporter;
use Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{
    #[Route('/book/create', name: 'app_book_create', methods: ['POST'])]
    public function create(Request $r): JsonResponse
    {
        $db = new Database();

        $data = json_decode($r->getContent());

        $db->query('
            INSERT INTO books (
            title,
            author,
            price,
            type
            ) VALUES (
            ' . $data['title'] . ',
            ' . $data['author'] . ',
            ' . $data['price'] . ',
            ' . $data['type'] . '
            )
        ');

        return $this->json(['message' => 'success']);
    }

    #[Route('/book/all', name: 'app_book_read_all')]
    public function readAll(): JsonResponse
    {
        $db = new Database();

        $data = $db->query('SELECT id, title, author, price, type FROM books');

        return $this->json($data);
    }

    #[Route('/book/{id}', name: 'app_book_read_one')]
    public function readOne($id): JsonResponse
    {
        $db = new Database();

        $data = $db->query('SELECT id, title, author, price, type FROM books WHERE id = ' . $id);

        return $this->json($data);
    }

    #[Route('/book/update', name: 'app_book_update', methods: ['POST'])]
    public function update(Request $re): JsonResponse
    {
        $db = new Database();

        $data = json_decode($re->getContent());

        $db->query('
            UPDATE books SET
            title = ' . $data['title'] . ',
            author = ' . $data['author'] . ',
            price = ' . $data['price'] . ',
            type = ' . $data['type'] . '
            WHERE id = ' . $data['id'] . '
        ');

        return $this->json(['message' => 'success']);
    }

    #[Route('/book/delete', name: 'app_book_update', methods: ['DELETE'])]
    public function delete(Request $re): JsonResponse
    {
        $db = new Database();

        $data = json_decode($re->getContent());

        $db->query('DELETE FROM books WHERE id = ' . $data['id']);

        return $this->json(['message' => 'success']);
    }

    #[Route('/book/reserve', name: 'app_book_reserve', methods: ['POST'])]
    public function reserve(Request $req): JsonResponse
    {
        $db = new Database();

        $data = json_decode($req->getContent());

        $tmp1 = $data['id']; // book id
        $tmp2 = $data['id2']; // customer id
        $tmp3 = $data['name']; // customer name

        $data = $db->query('SELECT id, title, author, price, type FROM books WHERE id = ' . $tmp1);

        if (count($data) == 0) {
            return $this->json(['message' => 'failed to find book with id ' . $tmp1]);
        }

        // We only allow type 1 (paperback books) not type 2 (hardcover books)
        // to be reserved.
        if ($data[0]['type'] == 1) {
            $db->query('
                INSERT INTO reservations (
                book_id,
                customer_id,
                customer_reservation_name
                ) VALUES (
                ' . $tmp1 . ',
                ' . $tmp2 . ',
                ' . $tmp3 . '
                )
            ');
            return $this->json(['message' => 'success']);
        } else if ($data[0]['type'] == 2) {
            return $this->json(['message' => 'failure']);
        }

        return $this->json(['message' => 'failure']);
    }

    #[Route('/book/export/{type}', name: 'app_book_export_all')]
    public function exportAll($type): Response
    {
        $db = new Database();

        $data = $db->query('SELECT id, title, author, price, type FROM books');

        if ($type == 'csv') {
           $exporter = new CSVExporter();
           $contentType = 'text/csv';
        } else if ($type == 'excel') {
            $exporter = new ExcelExporter();
            $contentType = 'application/vnd.ms-excel';
        } else if ($type == 'json') {
            $exporter = new JSONExporter();
            $contentType = 'application/json';
        }

        $data = $exporter->export($data);

        return new Response($data, 200, ['Content-Type' => $contentType]);
    }

    #[Route('/book/export/{type}/{id}', name: 'app_book_export_one')]
    public function exportOne($type, $id): Response
    {
        $db = new Database();

        $data = $db->query('SELECT id, title, author, price, type FROM books WHERE id = ' . $id);

        if ($type == 'csv') {
            $exporter = new CSVExporter();
            $contentType = 'text/csv';
        } else if ($type == 'excel') {
            $exporter = new ExcelExporter();
            $contentType = 'application/vnd.ms-excel';
        } else if ($type == 'json') {
            $exporter = new JSONExporter();
            $contentType = 'application/json';
        }

        $data = $exporter->export($data[0]);

        return new Response($data, 200, ['Content-Type' => $contentType]);
    }
}
