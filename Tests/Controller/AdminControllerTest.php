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

namespace LpDigital\Bundle\ConfigurationBundle\Tests\Controller;

use Doctrine\ORM\Tools\SchemaTool;

use BackBee\Site\Site;

use LpDigital\Bundle\ConfigurationBundle\Tests\ConfigurationTestCase;
use LpDigital\Bundle\ConfigurationBundle\Tests\Mock\MockAdminController;

/**
 * Tests suite for LpDigital\Bundle\ConfigurationBundle\Controller\AdminController.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 * @covers LpDigital\Bundle\ConfigurationBundle\Controller\AdminController
 */
class AdminControllerTest extends ConfigurationTestCase
{

    /**
     * @var MockAdminController
     */
    private $controller;

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

        $this->controller = new MockAdminController($this->bundle->getApplication());
        $this->controller->setBundle($this->bundle);
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Controller\AdminController::indexAction()
     */
    public function testIndexAction()
    {
        $response = $this->controller->indexAction();

        $this->assertTrue(is_string($response));
        $this->assertStringStartsWith('<section', $response);
        $this->assertStringEndsWith('section>', $response);

        $sections = $this->bundle->getSections();
        foreach ($sections as $label => $section) {
            $this->assertNotFalse(strpos($response, sprintf('href="#conf-section-%s"', $label)));
            $this->assertNotFalse(strpos($response, htmlentities(json_encode($section['elements']))));
        }
    }

    /**
     * @covers LpDigital\Bundle\ConfigurationBundle\Controller\AdminController::storeAction()
     */
    public function testStoreAction()
    {
        $this->controller->resetNotifications();
        $this->controller->storeAction('unknown');

        $error = [[
        'type' => 'error',
        'message' => 'Section not saved: Unknown section unknown.'
        ]];

        $this->assertEquals($error, $this->controller->getNotifications());

        $this->controller->resetNotifications();
        $response = $this->controller->indexAction();
        $success = [[
        'type' => 'success',
        'message' => 'Section saved.'
        ]];

        $this->assertEquals($response, $this->controller->storeAction('sample2'));
        $this->assertEquals($success, $this->controller->getNotifications());

        $this->controller->resetNotifications();
        $this->assertEquals($response, $this->controller->storeAction('conf-section-sample2'));
        $this->assertEquals($success, $this->controller->getNotifications());
    }
}
