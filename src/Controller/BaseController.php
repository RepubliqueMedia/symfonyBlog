<?php
/*
 * Create : php bin/console make:controller
 *
 *
 */

namespace App\Controller;


use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CategoryType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;

class BaseController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(ArticleRepository $repo)
    {
        //plus nécessaire
        //$repo = $this->getDoctrine()->getRepository(Article::class);
        //force l'user a être co
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $articles = $repo->findAll();
        /*
                $articles=[
                    [
                        'id'=>'1',
                        'title'=>'title article1',
                        'image'=>'//loremflickr.com/350/150/cat',
                        'intro'=>'intro de l\'article1',
                        'content'=>'<p>Parag 1 contenu article</p><p>ParaG 2 contenu article</p>'
                    ],
                    [
                        'id'=>'2',
                        'title'=>'title article2',
                        'image'=>'//loremflickr.com/350/150/dog',
                        'intro'=>'intro de l\'article2',
                        'content'=>'<p>Parag 2 contenu article2</p><p>ParaG 2 contenu article2</p>'
                    ],
                    [
                        'id'=>'3',
                        'title'=>'title article3',
                        'image'=>'//loremflickr.com/350/150/paris',
                        'intro'=>'intro de l\'article3',
                        'content'=>'<p>Parag 1 contenu article3</p><p>ParaG 2 contenu article3</p>'
                    ],
                ];
        */
        return $this->render('base/home.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'site_title' => 'Title du Site',// voir pour le config + tard
            'articles' => $articles,

        ]);
    }

    /**
     * @Route("/article/{id}", name="view_article", requirements={"id"="\d+"})
     */
    public function view_article(int $id, ArticleRepository $repo, Comment $comment = null, Request $request): Response
    {
        //plus nécessaire
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        //$article=$repo->findOneBy(['id'=>$id]);
        // Crazy auto generated
        $article = $repo->findOneById($id);
        //dump($article);
        dd($article->createdAt='2020-10-01');
        /*
        $article=[
            'id'=>1,
            'title'=>'title article',
            'image'=>'//loremflickr.com/350/150/cat',
            'intro'=>'intro de l\'article',
            'content'=>'<p>Parag 1 contenu article</p><p>ParaG 2 contenu article</p>'
        ];
        */

        //il faut que j'add le formulaire de comments
        if (!$comment) {
            $comment = new Comment();
        }

        $form = $this->createForm(CommentType::class, $comment);

        // on actualise notre form VS la request : autoload data dans $comments
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on affecte le comment a cet article
            $comment->setArticle($article);

            //on récup le manager d'entity
            $manager = $this->getDoctrine()->getManager();
            //on store
            $manager->persist($comment);
            $manager->flush();

        }

        return $this->render('base/article.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'article' => $article,
            'formComment' => $form->createView(),
        ]);
    }





    /**
     * @Route("/category/{id}", name="view_category", requirements={"id"="\d+"})
     */
    function view_category(int $id, Category $category): Response
    {
        /*
        $articles = $repo->findBy(['categories'=>$id]);
        dd($articles);
        ArticleRepository $repo
        */
        // TODO : recup via article repo + pagination
        // tout simplement
        $articles = $category->getArticles();

        return $this->render('base/home.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'site_title' => 'Category ' . $category->getName() . ' - Title du Site',// voir pour le config + tard
            'articles' => $articles,

        ]);
    }
}
