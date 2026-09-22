<?php

namespace EResponsable\SyliusMondialRelayPlugin;

use EResponsable\SyliusMondialRelayPlugin\DependencyInjection\Compiler\SyliusUiPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EResponsableSyliusMondialRelayPlugin
 */
class EResponsableSyliusMondialRelayPlugin extends Bundle
{
    use SyliusPluginTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SyliusUiPass());
    }
}
