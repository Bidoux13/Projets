<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
                'type'              => PasswordType::class,
                'invalid_message'   => 'Les mots de passe doivent correspondre.',
                'options'           => array('required' => true),
                'first_options'     => array('label' => 'Veuillez saisir un mot de passe :'),
                'second_options'    => array('label' => 'Saisissez le à nouveau :'),
                'constraints'       => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min'       => 5,
                        'minMessage'=> 'Password de minimum {{ limit }} caractères.',
            )))))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
