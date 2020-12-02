<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class,[
                'constraints'=>[
                    new Assert\Email(),
                ]
            ])
            ->add('password',PasswordType::class,[
                'constraints'=>[
                    new Assert\Length(['min'=>8]),
                ],

            ])
            ->add('_remember_me', CheckboxType::class, ['required' => false,'mapped' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        /*
        on a pas le _remember_me dans l'entity
        */
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);

    }
}
