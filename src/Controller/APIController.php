<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Users;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class APIController extends AbstractController
{
    /**
     * @Route("/api/articles/liste", name="api_list", methods={"GET"})
     */
    public function index(ArticlesRepository $articlesR)
    {
        // On récupère la liste des articles
        $articles = $articlesR->apiFindAll();

        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new Serializer($normalizers, $encoders);

        // On convertit en json
        $jsonContent = $serializer->serialize($articles, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;
    }

    /**
     * @Route("/api/article/lire/{id}", name="api_article", methods={"GET"})
     */
    public function getArticle(Articles $article)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($article, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/article/ajout", name="api_ajout", methods={"POST"})
     */
    public function addArticle(Request $request)
    {
        // On vérifie si la requête est une requête Ajax
        if($request->isXmlHttpRequest()) {
            // On instancie un nouvel article
            $article = new Articles();

            // On décode les données envoyées
            $donnees = json_decode($request->getContent());

            // On hydrate l'objet
            $article->setTitre($donnees->titre);
            $article->setContenu($donnees->contenu);
            $article->setFeaturedImage($donnees->image);
            $user = $this->getDoctrine()->getRepository(Users::class)->find(1);
            $article->setUsers($user);

            // On sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            // On retourne la confirmation
            return new Response('ok', 201);
        }
        return new Response('Failed', 404);
    }

    /**
     * @Route("/api/article/editer/{id}", name="api_edit", methods={"PUT"})
     */
    public function editArticle(?Articles $article, Request $request)
    {
        // On vérifie si la requête est une requête Ajax
        //if($request->isXmlHttpRequest()) {

            // On décode les données envoyées
            $donnees = json_decode($request->getContent());

            // On initialise le code de réponse
            $code = 200;

            // Si l'article n'est pas trouvé
            if(!$article){
                // On instancie un nouvel article
                $article = new Articles();
                // On change le code de réponse
                $code = 201;
            }

            // On hydrate l'objet
            $article->setTitre($donnees->titre);
            $article->setContenu($donnees->contenu);
            $article->setFeaturedImage($donnees->image);
            $user = $this->getDoctrine()->getRepository(Users::class)->find(1);
            $article->setUsers($user);

            // On sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            // On retourne la confirmation
            return new Response('ok', $code);
        //}
        return new Response('Failed', 404);
    }

    /**
     * @Route("/api/article/supprimer/{id}", name="api_supprime", methods={"DELETE"})
     */
    public function removeArticle(Articles $article)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return new Response('ok');
    }

}
