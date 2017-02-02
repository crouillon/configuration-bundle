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
use Symfony\Component\Yaml\Yaml;

use BackBee\Site\Site;
use BackBee\Utils\Collection\Collection;

/**
 * Tests suite for LpDigital\Bundle\ConfigurationBundle\Configuration.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 * @covers LpDigital\Bundle\ConfigurationBundle\Configuration
 */
class ConfigurationTest extends ConfigurationTestCase
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
            $em->getClassMetadata('BackBee\Site\Site'),
            $em->getClassMetadata('LpDigital\Bundle\ConfigurationBundle\Entity\Section'),
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
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::start()
     */
    public function testStart()
    {
        $this->assertInstanceOf('\Doctrine\ORM\EntityRepository', $this->getProperty($this->bundle, 'sections'));
        $this->assertEquals($this->site, $this->getProperty($this->bundle, 'site'));
        $this->assertEquals(
            Yaml::parse(file_get_contents(__DIR__ . '/Config/sections.yml')),
            $this->getProperty($this->bundle, 'conf')
        );
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::getSections()
     */
    public function testGetSection()
    {
        $default = $this->getProperty($this->bundle, 'conf');

        $this->assertEquals($default, $this->bundle->getSections());
        $this->assertEquals([], $this->bundle->getSections('unknown'));
        $this->assertEquals(['sample1' => $default['sample1']], $this->bundle->getSections('sample1'));
        $this->assertEquals(
            ['sample1' => $default['sample1'], 'sample4' => $default['sample4']],
            $this->bundle->getSections(['sample1', 'sample4'])
        );

        $this->bundle->setSection('sample2', ['text' => 'new value']);
        $sample2 = $this->bundle->getSections('sample2');
        $this->assertEquals('new value', Collection::get($sample2, 'sample2:elements:text:value'));
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::getSections()
     */
    public function testGetSectionTruncated()
    {
        $default = $this->getProperty($this->bundle, 'conf');
        $section = $this->invokeMethod($this->bundle, 'getStoredSection', ['sample4', true]);
        $this->bundle->getEntityManager()->flush($section);

        unset($default['sample4']['elements']['text2']);
        $this->setProperty($this->bundle, 'conf', $default);
        $this->assertEquals($default, $this->bundle->getSections());
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::setSection()
     */
    public function testSetSection()
    {
        $sample1 = $this->bundle->setSection('sample1', []);
        $this->assertInstanceOf('LpDigital\Bundle\ConfigurationBundle\Entity\Section', $sample1);
        $this->assertEquals('value', $sample1->getElementValue('text'));
        $this->assertFalse($this->bundle->getEntityManager()->getUnitOfWork()->isEntityScheduled($sample1));

        $sample2 = $this->bundle->setSection('sample2', ['text' => null]);
        $this->assertInstanceOf('LpDigital\Bundle\ConfigurationBundle\Entity\Section', $sample2);
        $this->assertEquals('', $sample2->getElementValue('text'));

        $sample3 = $this->bundle->setSection('sample3', ['text' => 'new value']);
        $this->assertInstanceOf('LpDigital\Bundle\ConfigurationBundle\Entity\Section', $sample3);
        $this->assertEquals('new value', $sample3->getElementValue('text'));
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::setSection()
     * @expectedException \InvalidArgumentException
     */
    public function testSetUnknownSection()
    {
        $this->bundle->setSection('unknown', []);
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::getStoredSection()
     */
    public function testGetStoredSection()
    {
        $this->assertNull($this->invokeMethod($this->bundle, 'getStoredSection', ['sample1']));

        $section = $this->invokeMethod($this->bundle, 'getStoredSection', ['sample1', true]);
        $this->assertInstanceOf('LpDigital\Bundle\ConfigurationBundle\Entity\Section', $section);
        $this->assertTrue($this->bundle->getEntityManager()->contains($section));
        $this->assertEquals('sample1', $section->getLabel());
        $this->assertEquals($this->site, $section->getSite());

        $this->bundle->getEntityManager()->flush($section);
        $this->bundle->getEntityManager()->clear();

        $reloaded =  $this->invokeMethod($this->bundle, 'getStoredSection', ['sample1', true]);
        $this->assertEquals($section->getUid(), $reloaded->getUid());
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::sanitizeConf()
     */
    public function testSanitizeConf()
    {
        $conf = [
            'sample1' => ['elements' => ['text' => ['type' => 'text', 'value' => 'Fake value']]],
            'unknown' => ['text' => ['type' => 'text', 'value' => 'Fake value']],
            'sample2' => 'invalid item',
            'sample3' => ['noelements' => ['text' => ['type' => 'text', 'value' => 'Fake value']]],
            'sample4' => ['elements' => 'not an array'],
        ];

        $expected = [
            'sample1' => ['elements' => ['text' => ['type' => 'text', 'value' => 'Fake value']]],
        ];

        $this->invokeMethod($this->bundle, 'sanitizeConf', [&$conf]);
        $this->assertEquals($expected, $conf);
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Configuration::sanitizeElements()
     */
    public function testSanitizeElements()
    {
        $elements = [
            'el1' => ['type' => 'text', 'value' => 'Fake value'],
            'iv1' => ['type' => 'unknown type'],
            'el2' => ['type' => 'datetimepicker', 'value' => null],
            'iv2' => [],
            'el3' => ['type' => 'nodeSelector'],
            'iv3' => '',
        ];

        $expected = [
            'el1' => ['type' => 'text', 'value' => 'Fake value'],
            'el2' => ['type' => 'datetimepicker', 'value' => 0],
            'el3' => ['type' => 'nodeSelector', 'value' => []],
        ];

        $this->invokeMethod($this->bundle, 'sanitizeElements', [&$elements]);
        $this->assertEquals($expected, $elements);
    }
}
