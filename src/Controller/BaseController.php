<?php
/*
 * Create : php bin/console make:controller
 *
 *
 */

namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/article/{id}", name="article", requirements={"id"="\d+"})
     */
    public function article(int $id, ArticleRepository $repo): Response
    {
        //plus nécessaire
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        //$article=$repo->findOneBy(['id'=>$id]);
        // Crazy auto generated
        $article = $repo->findOneById($id);
        //dump($article);
        /*
        $article=[
            'id'=>1,
            'title'=>'title article',
            'image'=>'//loremflickr.com/350/150/cat',
            'intro'=>'intro de l\'article',
            'content'=>'<p>Parag 1 contenu article</p><p>ParaG 2 contenu article</p>'
        ];
        */
        return $this->render('base/article.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/new",name="new_article")
     * @Route("/article/{id}/edit",name="edit_article", requirements={"id"="\d+"})
     */
    function manage_article(Request $request, Article $article=null): Response
    {
        /*
         WTF : tu as une route avec un id, cherche moi l'article correspondant auto
        */

        if(!$article){
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
            return $this->redirectToRoute('article', ['id' => $article->getId()]);
        }

        return $this->render('base/form_article.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'article' => $article,
            'formArticle' => $form->createView(),
            'editMode'=>($article->getId() ? true : false),//null = new
        ]);

    }

    /**
     * @Route("/category/new",name="new_category")
     * @Route("/category/{id}/edit",name="edit_category", requirements={"id"="\d+"})
     */
    function manage_category(Request $request, Category $category=null): Response{
        if(!$category){
            $category=new Category();
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
        return $this->render('base/form_category.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'category' => $category,
            'formCategory' => $form->createView(),
            'editMode'=>($category->getId() ? true : false),//null = new
        ]);
    }

    /**
     * @Route("/category/{id}", name="view_category", requirements={"id"="\d+"})
     */
    function view_category(int $id,Category $category): Response {
        /*
        $articles = $repo->findBy(['categories'=>$id]);
        dd($articles);
        ArticleRepository $repo
        */
        //tout simplement
        $articles=$category->getArticles();

        return $this->render('base/home.html.twig', [
            'site_name' => 'Nom du Site',// voir pour le config + tard
            'site_title' => 'Category '.$category->getName().' - Title du Site',// voir pour le config + tard
            'articles' => $articles,

        ]);
    }
}
