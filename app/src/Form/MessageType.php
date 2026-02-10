<?php

namespace App\Form;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', options: [
                'label' => 'Содержимое'
            ])
            ->add('chat', EntityType::class, [
                'class' => Chat::class,
                'choice_label' => 'id',
                'label' => 'Чат'
            ])
            ->add('sender', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'label' => 'Отправитель'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
