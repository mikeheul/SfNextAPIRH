<?php

namespace App\Controller;

use App\Form\EmployeeType;
use App\HttpClient\ApiHttpClient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/employees', name: 'app_employees')]
    public function employees(Request $request, ApiHttpClient $apiHttpClient, PaginatorInterface $paginator): Response
    {
        $employees = $apiHttpClient->getEmployees();

        $pagination = $paginator->paginate(
            $employees,
            $request->query->getInt('page', 1),
            6 
        );

        return $this->render('employee/index.html.twig', [
            'employees' => $pagination,
        ]);
    }

    #[Route('/employee/new', name: 'app_employee_new')]
    public function new(Request $request, ApiHttpClient $apiHttpClient)
    {
        $form = $this->createForm(EmployeeType::class, null, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();

            $firstName = $employee['firstName'];
            $lastName = $employee['lastName'];
            $email = $employee['email'];
            $position = $employee['position'];
            $startDate = $employee['startDate'];

            // Ajouter l'heure "00:00:00" à la date (ou l'heure actuelle si nécessaire)
            if ($startDate instanceof \DateTime) {
                // Vous pouvez ajuster l'heure si nécessaire
                $startDate->setTime(0, 0, 0);  // Définit l'heure à minuit
                $startDate = $startDate->format(\DateTime::ATOM); // Format ISO 8601 (ex: 2025-01-20T00:00:00Z)
            }

            $data = [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'position' => $position,
                'startDate' => $startDate
            ];

             // Envoyer un POST à votre API Next.js
            try {
                $employeeData = $apiHttpClient->addEmployee($data);
    
                // Si le livre a été ajouté avec succès
                $this->addFlash('success', 'Employee added successfully!');
                return $this->redirectToRoute('app_employees'); // Rediriger vers la page de la liste des livres
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_employee_new');
            }
        }

        return $this->render('employee/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}