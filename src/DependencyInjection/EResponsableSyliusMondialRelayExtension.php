<?php

declare(strict_types=1);

namespace EResponsable\SyliusMondialRelayPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EResponsableSyliusMondialRelayExtension
 */
class EResponsableSyliusMondialRelayExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('eResponsable_sylius_mondial_relay.wsdl', $config['wsdl'] ?? '');
        $container->setParameter('eResponsable_sylius_mondial_relay.merchant_id', $config['merchant_id'] ?? '');
        $container->setParameter('eResponsable_sylius_mondial_relay.private_key', $config['private_key'] ?? '');
        $container->setParameter('eResponsable_sylius_mondial_relay.base_url', $config['mondial_relay_base_url'] ?? '');
        $container->setParameter(
            'eResponsable_sylius_mondial_relay.enable_ticket_printing',
            $config['enable_ticket_printing'] ?? false
        );
        $container->setParameter('eResponsable_sylius_mondial_relay.map_provider', $config['map_provider'] ?? 'open_street_map');
        $container->setParameter('eResponsable_sylius_mondial_relay.google_api_key', $config['google_api_key'] ?? null);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sylius_twig_hooks')) {
            $hooksFile = __DIR__ . '/../Resources/config/app/twig_hooks/checkout/select_shipping.yaml';
            if (file_exists($hooksFile)) {
                $hooksConfig = Yaml::parseFile($hooksFile);
                if (isset($hooksConfig['sylius_twig_hooks'])) {
                    $container->prependExtensionConfig('sylius_twig_hooks', $hooksConfig['sylius_twig_hooks']);
                }
            }
        }
    }
}
