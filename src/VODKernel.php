<?php


use App\DependencyInjection\Compiler\CarrierTemplateDetectionPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class VODKernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);

        $confDir = $this->getProjectDir() . '/config';
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');

        $appConfDir = $this->getProjectDir() . '/src/App/Resources/config/';
        $loader->load($appConfDir . 'services.yml');
        $loader->load($appConfDir . 'admin.yml');
        $loader->load($appConfDir . 'twig.yml');
        $loader->load($appConfDir . 'repositories.yml');
        $loader->load($appConfDir . 'controllers.yml');
        $loader->load($appConfDir . 'listeners.yml');
        $loader->load($appConfDir . 'identification-bundle.yml');
        $loader->load($appConfDir . 'subscription-bundle.yml');
        $loader->load($appConfDir . 'carrier-templates.yml');
        $loader->load($appConfDir . 'commands.yml');

        $fixturesDir = $this->getProjectDir() . '/src/DataFixtures/config/';
        $loader->load($fixturesDir . 'fixtures.yml');


        // thats required to make a proper update of container in case of parameters file change.
        try {
            $loader->load($this->getProjectDir() . '/config/parameters.dist.php');
            $loader->load($this->getProjectDir() . '/config/parameters.php');
        } catch (\InvalidArgumentException $exception) {
            if ($this->getEnvironment() === 'dev') {

                $str = "You dont have `parameters.php` file inside of your config folder.\nPlease make copy of `parameters.dist.php` file and rename it into `parameters.php` \nAll environment related variables should be stored there";
                if (php_sapi_name() === 'cli') {
                    /*echo $str;*/
                } else {
                    throw new \RuntimeException($str);
                }
            }
        }
        $this->loadCustomParametersFile($container);

    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    private function loadCustomParametersFile(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (file_exists($this->getProjectDir() . '/config/parameters.php')) {
            $parameters = require $this->getProjectDir() . '/config/parameters.php';
        } else {
            $parameters = require $this->getProjectDir() . '/config/parameters.dist.php';
        }

        foreach ($parameters as $name => $default) {
            $value = getenv($name);

            if ($value === false) {
                $value = $default;
            }

            $value = trim($value);

            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }

            $container->setParameter(strtolower($name), $value);
        }

        $dest = dirname(__DIR__) . '/var/hash';
        if (file_exists($dest)) {
            $versionHash = file_get_contents($dest);
            $container->setParameter('app_version_hash', $versionHash);
        }


    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CarrierTemplateDetectionPass());
    }
}
