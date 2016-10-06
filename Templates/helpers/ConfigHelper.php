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

use BackBee\Renderer\AbstractRenderer;
use BackBee\Utils\Collection\Collection;

use LpDigital\Bundle\ConfigurationBundle\Configuration;

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
     * The Configuration bundle.
     *
     * @var Configuration
     */
    private $confBundle;

    /**
     * Class constructor.
     *
     * @param AbstractRenderer $renderer
     */
    public function __construct(AbstractRenderer $renderer)
    {
        parent::__construct($renderer);
        $this->confBundle = $this->getRenderer()->getApplication()->getBundle('conf');
    }

    /**
     * Returns an element value
     *
     * @param  $marker A path to set required value: section:element
     *
     * @return mixed
     */
    public function __invoke($marker)
    {
        if (false === strpos($marker, ':')) {
            return null;
        }

        list($label, $element) = explode(':', $marker, 2);
        $section = $this->confBundle->getSections($label);

        return Collection::get($section, sprintf('%s:elements:%s:value', $label, $element), null);
    }
}
