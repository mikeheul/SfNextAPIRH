<?php

namespace App\HttpClient;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiHttpClient extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpc)
    {
        $this->httpClient = $httpc;
    }

    public function getEmployees()
    {
        $response = $this->httpClient->request('GET', "/api/employee", [
            'verify_peer' => false
        ]);

        return $response->toArray();
    }

    public function getEmployee(string $id)
    {
        $response = $this->httpClient->request('GET', "/api/employee/$id", [
            'verify_peer' => false
        ]);

        return $response->toArray();
    }

    public function addEmployee(array $data)
    {
        try {
            $response = $this->httpClient->request('POST', '/api/employee', [
                'json' => $data, 
            ]);

            // Vérifier si la requête a réussi
            if ($response->getStatusCode() === 201) {
                return $response->toArray();
            }

            // Si le code de statut n'est pas 201, on lance une exception
            throw new \Exception('Error adding employee to database.');
        } catch (\Exception $e) {
            // Gérer l'exception si la requête échoue (erreur réseau, API hors ligne, etc.)
            throw new \Exception('An error occurred while adding the employee. Please try again later.');
        }
    }
}