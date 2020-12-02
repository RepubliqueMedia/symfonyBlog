<?php

/**
 *
 * Create : php bin/console make:form
 *
 */
namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         * Ici on pourrait définir les type de champs
         * les required & les constraints
         *
         * De base reprend la def de l'entity
         * Si défini évidemment prioritaire sur check entity
         *
         */
        $builder
            /*
            ->add('title',TextType::class,[
                'required'=>true,
                'constraints'=>[new Assert\Length(['min' => 3,'minMessage'=>"ok"])]
            ])
            */
            ->add('title')

            /*
             * https://symfony.com/doc/current/reference/forms/types/entity.html
             * expanded & multiple are type selector
             * expanded : true = radio(!multiple)|checkbox(multiple), false = select
             *
             */
            ->add('categories',EntityType::class, [
                'class'=>Category::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>true,
            ])
            ->add('image')
            ->add('intro')
            ->add('content')
            //->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
