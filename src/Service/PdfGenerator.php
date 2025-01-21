<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfGenerator
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Génère un PDF à partir d'un template Twig et de données.
     *
     * @param string $template Nom du template Twig
     * @param array $data Données à injecter dans le template
     * @param string $filename Nom du fichier PDF généré
     * @return Response Réponse contenant le PDF
     */
    public function generatePdf(string $template, array $data, string $filename): Response
    {
        // Configurer DomPDF
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        // Générer le contenu HTML à partir du template Twig
        $html = $this->twig->render($template, $data);

        // Charger le HTML dans DomPDF
        $dompdf->loadHtml($html);

        // Configurer la taille et l'orientation de la page
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Retourner le PDF comme réponse
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('inline; filename="%s"', $filename),
            ]
        );
    }
}
