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

namespace LpDigital\Bundle\ConfigurationBundle\Tests\Entity;

use BackBee\Site\Site;

use LpDigital\Bundle\ConfigurationBundle\Entity\Section;
use LpDigital\Bundle\ConfigurationBundle\Tests\ConfigurationTestCase;

/**
 * Tests suite for LpDigital\Bundle\ConfigurationBundle\Entity\Section.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section
 */
class SectionTest extends ConfigurationTestCase
{

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::__construct()
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::getUid()
     */
    public function testConstruct()
    {
        $this->assertEquals(32, strlen((new Section())->getUid()));

        $section = new Section('section_uid');
        $this->assertEquals('section_uid', $section->getUid());
        $this->assertInstanceOf('\DateTime', $this->getProperty($section, 'created'));
        $this->assertEquals($this->getProperty($section, 'modified'), $this->getProperty($section, 'created'));
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::getSite()
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::setSite()
     */
    public function testSite()
    {
        $section = new Section();
        $this->assertNull($section->getSite());

        $site = new Site('site_uid', ['label' => 'site_label']);
        $this->assertEquals($section, $section->setSite($site));
        $this->assertEquals($site, $section->getSite());
        $this->assertEquals($section, $section->setSite(null));
        $this->assertNull($section->getSite());
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::getLabel()
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::setLabel()
     */
    public function testLabel()
    {
        $section = new Section();
        $this->assertNull($section->getLabel());

        $this->assertEquals($section, $section->setLabel('label'));
        $this->assertEquals('label', $section->getLabel());
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::getElements()
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::setElements()
     */
    public function testElements()
    {
        $section = new Section();
        $this->assertEquals([], $section->getElements());

        $this->assertEquals($section, $section->setElements(['sample']));
        $this->assertEquals(['sample'], $section->getElements());
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::getElement()
     */
    public function testGetElement()
    {
        $section = new Section();
        $this->assertNull($section->getElement('unknown'));
        $section->setElements(['sample' => 'sample element']);

        $this->assertNull($section->getElement('unknown'));
        $this->assertEquals('sample element', $section->getElement('sample'));
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Entity\Section::getElementValue()
     */
    public function testGetElementValue()
    {
        $section = new Section();
        $section->setElements(['sample1' => 'sample element', 'sample2' => [], 'sample3' => ['value' => 'sample value']]);

        $this->assertNull($section->getElementValue('sample1'));
        $this->assertNull($section->getElementValue('sample2'));
        $this->assertEquals('sample value', $section->getElementValue('sample3'));
    }
}
