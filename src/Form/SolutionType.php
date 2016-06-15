<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 22:58
 */

namespace MttProjecteuler\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolutionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repository = $options['repository'];
        $choices = [
            'Отсутствует в списке...' => '',
        ];

        $choices = array_merge($choices, $repository->getLanguageChoices());

        $builder
            ->add('problemNumber')
            ->add('langId', ChoiceType::class, ['choices' => $choices, 'required' => false,])
            ->add('sourceLink')
            ->add('generate', CheckboxType::class, ['mapped' => false, 'required' => false,])
            ->add('executionTime')
            ->add('deviationTime')
            ->add('public', CheckboxType::class, ['required' => false,])
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'MttProjecteuler\Model\Solution',
            ])
            ->setRequired([
                'repository',
            ])
            ->setAllowedTypes(
                'repository', 'MttProjecteuler\Database\Repository'
            )
        ;
    }
}
