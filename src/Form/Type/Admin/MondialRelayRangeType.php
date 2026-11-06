<?php

namespace EResponsable\SyliusMondialRelayPlugin\Form\Type\Admin;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class MondialRelayRangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minWeight', NumberType::class, [
                'label' => 'sylius.form.shipping_calculator.mondial_relay.min_weight',
                'required' => false,
                'scale' => 3,
                'html5' => true,
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0, 'groups' => ['sylius']]),
                ],
            ])
            ->add('maxWeight', NumberType::class, [
                'label' => 'sylius.form.shipping_calculator.mondial_relay.max_weight',
                'required' => false,
                'scale' => 3,
                'html5' => true,
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0, 'groups' => ['sylius']]),
                ],
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'sylius.form.shipping_calculator.mondial_relay.shipping_cost',
                'currency' => $options['currency'],
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                    new GreaterThanOrEqual(['value' => 0, 'groups' => ['sylius']]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('currency');
    }
}
