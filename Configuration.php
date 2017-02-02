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

namespace LpDigital\Bundle\ConfigurationBundle;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

use BackBee\Bundle\AbstractBundle;
use BackBee\Site\Site;

use LpDigital\Bundle\ConfigurationBundle\Entity\Section;

/**
 * Configuration bundle
 *
 * @category        Bundle
 * @manufacturer    Lp digital - http://www.lp-digital.fr
 * @copyright       ©2017 - Lp digital
 * @author          Cédric Bouillot (CBO) <cedric.bouillot@lp-digital.fr>
 */
class Configuration extends AbstractBundle
{

    /**
     * Element types handled with their default empty value.
     *
     * @var array
     */
    protected $handledTypes = [
        'text' => '',
        'password' => '',
        'textarea' => '',
        'datetimepicker' => 0,
        'checkbox' => [],
        'radio' => [],
        'select' => [],
        'nodeSelector' => [],
        'mediaSelector' => [],
        'linkSelector' => [],
    ];

    /**
     * Entity repository for sections.
     *
     * @var EntityRepository
     */
    protected $sections;

    /**
     * Current Site.
     *
     * @var Site
     */
    protected $site;

    /**
     * Configuration provided sections.
     *
     * @var array
     */
    protected $conf;

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->sections = $this->getEntityManager()->getRepository(Section::class);
        $this->site = $this->getApplication()->getSite();
        $this->conf = $this->getConfig()->getSection('sections');
        $this->sanitizeConf($this->conf);
    }

    /**
     * Get sections data from config.
     *
     * @param  mixed   $section           Section(s) to get.
     *
     * @return array                      Array of sections with fields/values.
     *
     * @throws \RuntimeException Thrown if he bundle is not installed.
     */
    public function getSections($section = [])
    {
        if (!$this->isInstalled()) {
            throw new \RuntimeException(
                sprintf('Configuration bundle is not install, please run `./backbee bundle:install %s`', $this->getId())
            );
        }

        if (!is_array($section)) {
            $section = [$section];
        }

        $sections = [];
        foreach ($this->conf as $key => $item) {
            if (!empty($section) && !in_array($key, $section)) {
                continue;
            }

            $sections[$key] = $item;
            $validMarkers = array_keys($sections[$key]['elements']);
            if (null !== $stored = $this->getStoredSection($key)) {
                $sections[$key]['elements'] = array_merge($sections[$key]['elements'], $stored->getElements());
            }

            $invalidMarkers = array_diff(array_keys($sections[$key]['elements']), $validMarkers);
            foreach ($invalidMarkers as $marker) {
                unset($sections[$key]['elements'][$marker]);
            }
        }

        $this->sanitizeConf($sections);

        return $sections;
    }

    /**
     * Set section data from provided data.
     *
     * @param  string $label  Item to set properties for.
     * @param  array  $values The values to be set.
     *
     * @return Section        The updated section entity.
     *
     * @throws \RuntimeException Thrown if he bundle is not installed.
     */
    public function setSection($label, array $values)
    {
        if (!$this->isInstalled()) {
            throw new \RuntimeException(
                sprintf('Configuration bundle is not install, please run `./backbee bundle:install %s`', $this->getId())
            );
        }

        if (!isset($this->conf[$label])) {
            throw new \InvalidArgumentException(sprintf('Unknown section %s.', $label));
        }

        $elements = [];
        $section = $this->getStoredSection($label, true);
        foreach ($this->conf[$label]['elements'] as $key => $element) {
            $value = isset($values[$key]) ? $values[$key] : null;
            $element['value'] = null !== $value ? $value :($section->getElementValue($key) ?: $element['value']);
            $elements[$key] = $element;
        }

        $section->setElements($elements);
        $this->getEntityManager()->flush($section);

        return $section;
    }

    /**
     * Return the stored section $label if found and persist not asked, null otherwise.
     *
     * @param  string       $label             The section to look for.
     * @param  boolean      $persistIfNotFound Optional, persists a new section $label
     *                                         if need (default: false)
     *
     * @return Section|null
     */
    private function getStoredSection($label, $persistIfNotFound = false)
    {
        $section = $this->sections->findOneBy(['site' => $this->site, 'label' => $label]);

        if (true === $persistIfNotFound && null === $section) {
            $section = new Section();

            $section->setSite($this->site)
                    ->setLabel($label)
                    ->setElements($this->conf[$label]['elements']);

            $this->getEntityManager()->persist($section);
        }

        return $section;
    }

    /**
     * Sanitizes section configuration.
     *
     * @param array $conf
     */
    private function sanitizeConf(array &$conf)
    {
        foreach ($conf as $section => $item) {
            if (!isset($this->conf[$section])
                || !is_array($item)
                || !isset($item['elements'])
                || !is_array($item['elements'])
            ) {
                unset($conf[$section]);
                continue;
            }

            $this->sanitizeElements($conf[$section]['elements']);
        }
    }

    /**
     * Sanitizes section elements.
     *
     * @param array $elements
     */
    private function sanitizeElements(array &$elements)
    {
        $availableTypes = array_keys($this->handledTypes);
        foreach ($elements as $label => $element) {
            if (!is_array($element) || !isset($element['type']) || !in_array($element['type'], $availableTypes)) {
                unset($elements[$label]);
                continue;
            }

            if (!isset($element['value']) || empty($element['value'])) {
                $elements[$label]['value'] = $this->handledTypes[$element['type']];
            }
        }
    }

    /**
     * Checks whether the bundle is properly installed.
     *
     * @return boolean
     */
    private function isInstalled()
    {
        try {
            $this->sections->find('0');
        } catch (DBALException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function stop()
    {
    }
}
