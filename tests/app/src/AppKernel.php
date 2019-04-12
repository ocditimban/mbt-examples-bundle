<?php

namespace App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Dunglas\DoctrineJsonOdm\Bundle\DunglasDoctrineJsonOdmBundle;
use Oneup\FlysystemBundle\OneupFlysystemBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tienvx\Bundle\MbtBundle\TienvxMbtBundle;
use Tienvx\Bundle\MbtExamplesBundle\TienvxMbtExamplesBundle;

class AppKernel extends Kernel
{
    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles()
    {
        $bundles = array(
            new SecurityBundle(),
            new FrameworkBundle(),
            new DoctrineBundle(),
            new MonologBundle(),
            new DunglasDoctrineJsonOdmBundle(),
            new OneupFlysystemBundle(),
            new SwiftmailerBundle(),
            new TienvxMbtBundle(),
            new TienvxMbtExamplesBundle(),
        );

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(dirname(__DIR__).'/config/config.yaml');
        $loader->load(dirname(__DIR__).'/config/services.yaml');
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->environment;
    }

    public function getProjectDir()
    {
        return dirname(__DIR__);
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/log';
    }
}
