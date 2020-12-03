<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * autorise que le role admin pour ce controller
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /* CATEGORY */

    /**
     * @Route("/category/new",name="new_category")
     * @Route("/category/{id}/edit",name="edit_category", requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    function manage_category(Request $request, Category $category = null): Response
    {
        if (!$category) {
            $category = new Category();
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on récup le manager d'entity
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('article', ['id' => $category->getId()]);
        }
        return $this->render('admin/form_category.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'category' => $category,
            'formCategory' => $form->createView(),
            'editMode' => ($category->getId() ? true : false),//null = new
        ]);
    }

    /* ARTICLE */
    /**
     * @Route("/article/new",name="new_article")
     * @Route("/article/{id}/edit",name="edit_article", requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    function manage_article(Request $request, Article $article = null): Response
    {
        /*
         WTF : tu as une route avec un id, cherche moi l'article correspondant auto
        */

        if (!$article) {
            // on init un objet article vide
            $article = new Article();
        }


        /*
         * createFormBuilder : fabriquer logiciellement un form a partir d'une Entity
         */
        /*
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->getForm();
        */
        /*
         * createForm : utilise un modèle de formulaire
         *   -> gen un formulaire php bin/console make:form
         */
        $form = $this->createForm(ArticleType::class, $article);

        // on actualise notre form VS la request ?
        $form->handleRequest($request);
        //dump($article); // c'est ouf mais le handle a load les datas dans $article
        if ($form->isSubmitted() && $form->isValid()) {
            //$article=$form->getData(); // useless, $article déjà = aux datas
            /*
                        Maintenant géré direct dans Entity
                        if(!$article->getId()){
                            //on add la date a notre article vu qu'on a pas le champ
                            $article->setCreatedAt(new \DateTime());
                        }

                        $article->setUpdatedAt(new \DateTime());
            */

            //on récup le manager d'entity
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();
            //dump($article);
            return $this->redirectToRoute('view_article', ['id' => $article->getId()]);
        }

        return $this->render('admin/form_article.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'article' => $article,
            'formArticle' => $form->createView(),
            'editMode' => ($article->getId() ? true : false),//null = new
        ]);

    }

    /* Manage comments */

}
