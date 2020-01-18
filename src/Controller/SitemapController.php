<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request)
    {
        // Nous récupérons le nom d'hôte depuis l'URL
        $hostname = $request->getSchemeAndHttpHost();

        // On initialise un tableau pour lister les URLs
        $urls = [];

        // On ajoute les URLs "statiques"
        $urls[] = ['loc' => $this->generateUrl('accueil')];
        $urls[] = ['loc' => $this->generateUrl('app_register')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];

        // add dynamic urls, like blog posts from your DB
        foreach ($this->getDoctrine()->getRepository(Articles::class)->findAll() as $article) {
            $created_at = $article->getCreatedAt();

            $images = [
                'loc' => '/uploads/images/featured/'.$article->getFeaturedImage(), // URL to image
                'title' => $article->getTitre()    // Optional, text describing the image
            ];

            $urls[] = [
                'loc' => $this->generateUrl('actualites_article', [
                    'slug' => $article->getSlug(),
                ]),
                'lastmod' => $article->getUpdatedAt()->format('Y-m-d'),
                'image' => $images
            ];
        }


        // Fabrication de la réponse XML
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname]
            )
        );

        // Ajout des entêtes
        $response->headers->set('Content-Type', 'text/xml');

        // On envoie la réponse
        return $response;
    }
}
