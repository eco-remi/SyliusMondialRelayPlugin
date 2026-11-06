<?php

namespace EResponsable\SyliusMondialRelayPlugin\Form\Type\Admin;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class MondialRelayConfigurationType extends AbstractType
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * MondialRelayConfigurationType constructor.
     *
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ranges', LiveCollectionType::class, [
                'label' => 'sylius.form.shipping_calculator.mondial_relay.weight_ranges',
                'entry_type' => MondialRelayRangeType::class,
                'entry_options' => ['currency' => $this->channelContext->getChannel()->getBaseCurrency()->getCode()],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'error_bubbling' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'limit' => 10,
            ])
            ->setAllowedTypes('limit', 'integer')
        ;
    }
}
