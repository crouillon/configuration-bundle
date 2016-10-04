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

namespace LpDigital\Bundle\ConfigurationBundle;

use Symfony\Component\HttpFoundation\Response;

use BackBee\Bundle\AbstractBundle;

use LpDigital\Bundle\ConfigurationBundle\Entity\Section;

/**
 * Configuration bundle
 *
 * @category        Bundle
 * @manufacturer    Lp digital - http://www.lp-digital.fr
 * @copyright       ©2015 - Lp digital
 * @author          Cédric Bouillot (CBO) <cedric.bouillot@lp-digital.fr>
 */
class Configuration extends AbstractBundle
{

    /**
     * @var \Doctrine\ORM\Repository\Repository
     */
    protected $sections;

    /**
     * Current Site
     * @var BackBee\Site\Site
     */
    protected $site;

    /**
     * Configuration provided sections
     * @var Array
     */
    protected $conf;

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->sections = $this->getEntityManager()->getRepository('LpDigital\Bundle\ConfigurationBundle\Entity\Section');
        $this->site = $this->getApplication()->getSite();
        $this->conf = $this->getConfig()->getSection('sections');
    }

    /**
     * Get sections data from config
     * @param mixed $section Section(s) to get
     * @return array Array of sections with fields/values
     */
    public function getSections($section = null)
    {
        if ($section && !is_array($section)) {
            $section = array($section);
        }
        $sections = array();
        foreach ($this->conf as $key => $item) {
            if (!$section || in_array($key, $section)) {
                if (null !== $stored = $this->sections->findOneBy(array('site' => $this->site, 'label' => $key))) {
                    foreach ($stored->getElements() as $label => $element) {
                        if (isset($item['elements'][$label])) {
                            $item['elements'][$label] = $element;
                        }
                    }
                }
            }
            $sections[$key] = $item;
        }
        return $sections;
    }

    /**
     * Set sections data from provided data
     * @param {string} $label Item to set properties for
     * @return array Array of sections with fields/values
     */
    public function setSection($label)
    {
        if (!isset($this->conf[$label])) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        if (null === $section = $this->sections->findOneBy(array('site' => $this->site, 'label' => $label))) {
            $section = new Section();
            $section->setSite($this->site)->setLabel($label);
        }
        $elements = array();
        foreach ($this->conf[$label]['elements'] as $key => $element) {
            if (null !== ($val = $this->getApplication()->getRequest()->request->get($key))) {
                $element['value'] = $val;
            } elseif (null !== ($val = $section->getElementValue($key))) {
                $element['value'] = $val;
            }
            $elements[$key] = $element;
        }
        $section->setElements($elements);
        $this->getEntityManager()->persist($section);
        $this->getEntityManager()->flush();
        return new Response('', Response::HTTP_OK);
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {

    }

}
