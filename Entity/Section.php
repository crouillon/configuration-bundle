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

namespace LpDigital\Bundle\ConfigurationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use BackBee\Site\Site;

/**
 * Configuration section entity
 *
 * @category    Entity
 * @package     BackBee\Bundle\ConfigurationBundle
 * @copyright   ©2015 - Lp digital - http://www.lp-digital.fr
 * @author      Cédric Bouillot (CBO) <cedric.bouillot@lp-digital.fr>
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="bbx_configuration",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uniq_conf_site_label",columns={"site", "label"})
 *      },
 *      indexes={
 *          @ORM\Index(name="key_conf_site", columns={"site"})
 *      }
 * )
 */
class Section
{

    /**
     * Section unique identifier
     * @var string
     * @ORM\Id
     * @ORM\Column(
     *      type="string",
     *      length=32,
     *      unique=true,
     *      nullable=false
     * )
     */
    private $uid;

    /**
     * Section site
     * @var \Backbee\Site\site
     * @ORM\ManyToOne(
     *      targetEntity="BackBee\Site\Site",
     *      fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinColumn(
     *      name="site",
     *      referencedColumnName="uid",
     *      nullable=false
     * )
     */
    private $site;

    /**
     * Section label
     * @var string
     * @ORM\Column(
     *      type="string",
     *      length=255,
     *      nullable=false
     * )
     */
    private $label;

    /**
     * Section elements as serialized array
     * @var array
     * @ORM\Column(
     *      type="array",
     *      nullable=false
     * )
     */
    protected $elements = array();

    /**
     * Section creation date
     * @var \DateTime
     * @ORM\Column(
     *      type="datetime",
     *      nullable=false
     * )
     */
    private $created;

    /**
     * Section last modification date
     * @var \DateTime
     * @ORM\Column(
     *      type="datetime",
     *      name="modified",
     *      nullable=false
     * )
     */
    private $modified;

    /**
     * Class constructor
     * @param string $uid
     * @param array $props
     */
    public function __construct($uid = null, array $props = null)
    {
        $this->uid = (is_null($uid)) ? md5(uniqid('', true)) : $uid;
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /**
     * Getter for publication unique id
     * @param {none}
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Getter for section site
     * @return string Section site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Setter for section site
     * @param \BackBee\Site\Site $site
     * @return \BackBee\Bundle\ConfigurationBundle\Entity\Section User for chaining
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * Get section label
     * @return string Section label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set section label
     * @var string $label
     * @return \BackBee\Bundle\ConfigurationBundle\Entity\Section User for chaining
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get section elements
     * @return array Section data
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set section elements
     * @param array $elements Section elements
     * @return \BackBee\Bundle\ConfigurationBundle\Entity\Section User for chaining
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * Retrieve provided element from section elements
     * @param string $element Element to get
     * @return string The element
     */
    public function getElement($element)
    {
        return (isset($this->elements[$element]) ? $this->elements[$element] : null);
    }

    /**
     * Retrieve provided element value from section elements
     * @param string $element Element to get value from
     * @return string The element value
     */
    public function getElementValue($element)
    {
        return (isset($this->elements[$element]) ? $this->elements[$element]['value'] : null);
    }

}