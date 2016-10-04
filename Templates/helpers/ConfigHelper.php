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

namespace BackBee\Renderer\Helper;

/**
 * Config helper
 * Provide configuration functions to handle configuration parameters
 *
 * @category  Helper
 * @package   Publishpaper\ConfigurationBundle\Helpers
 * @copyright © 2015 - Lp digital (http://www.lp-digital.fr)
 * @author    Cédric Bouillot (CBO) <cedric.bouillot@lp-digital.fr>
 */
class ConfigHelper extends AbstractHelper
{

    /**
     * @var BackBee\BBApplication
     */
    protected $app;

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
     * Class constructor.
     * @param \BackBee\Renderer\AbstractRenderer $renderer
     */
    /*
      public function __construct(BBApplication $app, AbstractRenderer $renderer)
      {
      parent::__construct($renderer);
      $this->app = $app;
      }
     */

    /**
     * Helper invocation
     * @param none
     * @return \BackBee\Renderer\Helper\page
     */
    public function __invoke()
    {
        $this->app = $this->getRenderer()->getApplication();
        $this->site = $this->app->getSite();
        $this->sections = $this->app->getEntityManager()->getRepository('BackBee\Bundle\ConfigurationBundle\Entity\Section');
        return $this;
    }

    /**
     * Get the value of provided configuration marker
     * @return mixed
     */
    public function getValue($marker)
    {
        list($label, $element) = explode(':', $marker);
        $sections = $this->app->getBundle('conf')->getSections();
        $section = $this->sections->findOneBy(array('site' => $this->site, 'label' => $label));
        if ($section && null !== ($val = $section->getElementValue($element))) {
            return $val;
        } elseif (isset($sections[$label]['elements'][$element]['value'])) {
            return $sections[$label]['elements'][$element]['value'];
        } else {
            return null;
        }
    }

}
