<?php
/*
 * Create Fixture : php bin/console make:fixtures
 *
 * Load Fixture : php bin/console doctrine:fixtures:load
 *
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Article;
use App\Entity\Category;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        //$categories = new ArrayCollection();//pas de shuffle
        $categories = [];
        for ($i = 1; $i <= 5; $i++) {
            //on fabrique des categories
            $category = (new Category())
                ->setName($faker->word())
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            $categories[]=$category;
        }

        for ($j = 1; $j <= 20; $j++) {
            //on fabrique des articles
            $article = new Article();
            $content = '<p>' . implode('</p><p>', $faker->paragraphs()) . '</p>';
            $date = $faker->dateTimeBetween('- 30 days');
            shuffle($categories);
            $cats=array_slice($categories,0,mt_rand(1,3));
            $article->title=($faker->sentence());
            $article
                //->setTitle($faker->sentence())

                ->setContent($content)
                ->setImage($faker->imageUrl())
                ->setIntro($faker->paragraph(1))
                ->setCreatedAt($date)
                ->addCategories(new ArrayCollection($cats));

            $manager->persist($article);

            //on add des comments
            for ($k = 1; $k <= mt_rand(2, 4); $k++) {
                $content = '<p>' . implode('</p><p>', $faker->paragraphs(1)) . '</p>';
                $comment=(new Comment())
                    ->setAuthor($faker->firstName())
                    ->setEmail($faker->email)
                    ->setArticle($article)
                    ->setContent($content)
                    ;
                $manager->persist($comment);

            }

        }


        $manager->flush();
    }
}
