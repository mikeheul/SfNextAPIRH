<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Service\PdfGenerator;
use App\HttpClient\ApiHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentController extends AbstractController
{
    #[Route('/employee/{id}/contract', name: 'employee_contract')]
    public function generateContract(string $id, ApiHttpClient $apiHttpClient, PdfGenerator $pdfGenerator): Response
    {
         // Appeler l'API Next.js pour récupérer les informations de l'employé
        try {
            $response = $apiHttpClient->getEmployee($id);
            $employee = $response;
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Employee not found or API error: ' . $e->getMessage());
        }

        $filename = sprintf('contract_%s_%s.pdf', strtolower($employee['firstName']), strtolower($employee['lastName']));
        return $pdfGenerator->generatePdf('pdf/contract.html.twig', ['employee' => $employee], $filename);
    }

    #[Route('/employee/{id}/employment-certificate', name: 'employment_certificate')]
    public function generateEmploymentCertificate(string $id, ApiHttpClient $apiHttpClient, PdfGenerator $pdfGenerator): Response
    {
        try {
            $response = $apiHttpClient->getEmployee($id);
            $employee = $response;
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Employee not found or API error: ' . $e->getMessage());
        }

        // Définir le nom du fichier
        $filename = sprintf('employment_certificate_%s_%s.pdf', strtolower($employee['firstName']), strtolower($employee['lastName']));
        
        // Utiliser le service PdfGenerator pour générer le PDF
        return $pdfGenerator->generatePdf('pdf/employment_certificate.html.twig', ['employee' => $employee], $filename);
    }

    #[Route('/employee/{id}/payslip', name: 'employee_payslip')]
    public function generatePayslip(string $id, ApiHttpClient $apiHttpClient, PdfGenerator $pdfGenerator): Response
    {
        try {
            $response = $apiHttpClient->getEmployee($id);
            $employee = $response;
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Employee not found or API error: ' . $e->getMessage());
        }

        // Simuler les données de paie pour cet employé
        $payroll = [
            'grossSalary' => 3500,    // Exemple : Salaire brut
            'deductions' => 700,     // Exemple : Retenues
            'netSalary' => 2800,     // Exemple : Salaire net
        ];

        // Définir le nom du fichier
        $filename = sprintf('payslip_%s_%s.pdf', strtolower($employee['firstName']), strtolower($employee['lastName']));
        
        // Utiliser le service PdfGenerator pour générer le PDF
        return $pdfGenerator->generatePdf('pdf/simplified_payslip.html.twig', [
            'employee' => $employee,
            'payroll' => $payroll
        ], $filename);
    }
}
