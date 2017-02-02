<?php

/*
 * Copyright (c) 2017 Lp digital system
 *
 * This file is part of ConfigurationBundle.
 *
 * ConfigurationBundle is free bundle: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ConfigurationBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ConfigurationBundle. If not, see <http://www.gnu.org/licenses/>.
 */

namespace LpDigital\Bundle\ConfigurationBundle\Tests;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;

use BackBee\Tests\Mock\MockBBApplication;

use LpDigital\Bundle\ConfigurationBundle\Configuration;

/**
 * Test case for Configuration bundle.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 */
class ConfigurationTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Configuration
     */
    protected $bundle;

    /**
     * Sets up the required fixtures.
     */
    public function setUp()
    {
        $mockConfig = [
            'ClassContent' => [],
            'Config' => [
                'bundle' => ['conf' => ['config.yml' => file_get_contents(__DIR__ . '/Config/sections.yml')]],
                'bootstrap.yml' => file_get_contents(__DIR__ . '/Config/bootstrap.yml'),
                'bundles.yml' => file_get_contents(__DIR__ . '/Config/bundles.yml'),
                'config.yml' => file_get_contents(__DIR__ . '/Config/config.yml'),
                'doctrine.yml' => file_get_contents(__DIR__ . '/Config/doctrine.yml'),
                'logging.yml' => file_get_contents(__DIR__ . '/Config/logging.yml'),
                'security.yml' => file_get_contents(__DIR__ . '/Config/security.yml'),
                'services.yml' => file_get_contents(__DIR__ . '/Config/services.yml'),
            ],
            'Ressources' => [],
            'cache' => [
                'Proxies' => [],
                'twig' => []
            ],
        ];
        vfsStream::umask(0000);
        vfsStream::setup('repositorydir', 0777, $mockConfig);
        $mockApp = new MockBBApplication(null, null, false, $mockConfig, __DIR__ . '/../vendor');
        $this->bundle = $mockApp->getBundle('conf');
        $this->bundle
            ->getConfig()
            ->setSection('sections', Yaml::parse(file_get_contents(__DIR__ . '/Config/sections.yml')));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param  object &$object    Instantiated object that we will run method on.
     * @param  string $methodName Method name to call.
     * @param  array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @link https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Call protected/private property of a class.
     *
     * @param  object &$object      Instantiated object.
     * @param  string $propertyName Method name to call.
     *
     * @return mixed Property value.
     */
    public function getProperty(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * Set a protected/private property of a class.
     *
     * @param  object &$object      Instantiated object.
     * @param  string $propertyName Method name to call.
     * @param  mixed  $value        The value to be setted.
     */
    public function setProperty(&$object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($object, $value);
    }
}
