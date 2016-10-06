<?php

/*
 * Copyright (c) 2016 Lp digital system
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

namespace LpDigital\Bundle\ConfigurationBundle\Tests\Helper;

use Doctrine\ORM\Tools\SchemaTool;

use BackBee\Renderer\Helper\ConfigHelper;
use BackBee\Site\Site;

use LpDigital\Bundle\ConfigurationBundle\Tests\ConfigurationTestCase;

/**
 * Tests suite for BackBee\Renderer\Helper\ConfigHelper.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 * @covers BackBee\Renderer\Helper\ConfigHelper
 */
class ConfigHelperTest extends ConfigurationTestCase
{

    /**
     * @var ConfigHelper
     */
    private $helper;

    /**
     * Sets up the required fixtures.
     */
    public function setUp()
    {
        parent::setUp();

        $em = $this->bundle->getEntityManager();

        $metadata = [
            $em->getClassMetadata('BackBee\Site\Site'),
            $em->getClassMetadata('LpDigital\Bundle\ConfigurationBundle\Entity\Section'),
        ];
        $schema = new SchemaTool($em);
        $schema->createSchema($metadata);

        $site = new Site('site_uid', ['label' => 'site label']);
        $em->persist($site);
        $em->flush($site);

        $this->bundle->getApplication()->getContainer()->set('site', $site);
        $this->bundle->start();

        $this->helper = new ConfigHelper($this->bundle->getApplication()->getRenderer());
    }

    /**
     * @covers BackBee\Renderer\Helper\ConfigHelper::__construct()
     */
    public function testConstruct()
    {
        $this->assertEquals($this->bundle, $this->getProperty($this->helper, 'confBundle'));
    }

    /**
     * @covers BackBee\Renderer\Helper\ConfigHelper::__invoke()
     */
    public function testInvoke()
    {
        $helper = $this->helper;

        $this->assertNull($helper('nomarker'));
        $this->assertNull($helper('unknown:element'));
        $this->assertNull($helper('sample1:element:unknown'));
        $this->assertEquals('value', $helper('sample1:text'));
    }
}
