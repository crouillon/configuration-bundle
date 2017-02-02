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

use Doctrine\ORM\Tools\SchemaTool;

use BackBee\Site\Site;

/**
 * Tests suite for LpDigital\Bundle\ConfigurationBundle\Configuration.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 * @covers LpDigital\Bundle\ConfigurationBundle\Configuration
 */
class ConfigurationNotInstalledTest extends ConfigurationTestCase
{

    /**
     * @var Site
     */
    private $site;

    /**
     * Sets up the required fixtures.
     */
    public function setUp()
    {
        parent::setUp();

        $em = $this->bundle->getEntityManager();

        $metadata = [
            $em->getClassMetadata('BackBee\Site\Site')
        ];
        $schema = new SchemaTool($em);
        $schema->createSchema($metadata);

        $this->site = new Site('site_uid', ['label' => 'site label']);
        $em->persist($this->site);
        $em->flush($this->site);

        $this->bundle->getApplication()->getContainer()->set('site', $this->site);
        $this->bundle->start();
    }

    /**
     * @covers                   LpDigital\Bundle\ConfigurationBundle\Configuration::getSections()
     * @covers                   LpDigital\Bundle\ConfigurationBundle\Configuration::isInstalled()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Configuration bundle is not install, please run `./backbee bundle:install conf`
     */
    public function testGetSections()
    {
        $this->bundle->getSections();
    }

    /**
     * @covers                   LpDigital\Bundle\ConfigurationBundle\Configuration::setSection()
     * @covers                   LpDigital\Bundle\ConfigurationBundle\Configuration::isInstalled()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Configuration bundle is not install, please run `./backbee bundle:install conf`
     */
    public function testSetSection()
    {
        $this->bundle->setSection('sample', []);
    }
}
