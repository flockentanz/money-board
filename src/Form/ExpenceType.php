<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Expence;
use App\Entity\User;
use phpDocumentor\Reflection\Types\False_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Название операции'
            ))
            ->add('amount', MoneyType::class,array(
                'label' => 'Сумма',
                'currency' => False,
            ))
            ->add('category', EntityType::class, array(
                'label' => 'Категория',
                'class' => Category::class,
                'choice_label' => 'title',
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Добавить операцию',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ))
            ->add('delete', SubmitType::class, array(
                'label' => 'Удалить',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Expence::class,
        ]);
    }
}
