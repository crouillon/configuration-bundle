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

namespace LpDigital\Bundle\ConfigurationBundle\Controller;

use BackBee\Bundle\AbstractAdminBundleController;

/**
 * Configuration Admin Controller.
 * Handles Confguration admin related actions.
 *
 * @category    Controller
 * @copyright   ©2015 - Lp digital - http://www.lp-digital.fr
 * @author      Cédric Bouillot (CBO) <cedric.bouillot@lp-digital.fr>
 */
class AdminController extends AbstractAdminBundleController
{

    /**
     * Configuration default admin.
     *
     * @return string HTML rendered default admin template
     */
    public function indexAction()
    {
        return $this->render('admin/index.twig', ['sections' => $this->getBundle()->getSections()]);
    }

    /**
     * Store provided section values if modified (ie not null).
     *
     * @param  string $label
     *
     * @return string
     */
    public function storeAction($label = null)
    {
        try {
            if (0 === strpos($label, 'conf-section')) {
                $label = substr($label, 13);
            }

            $this->getBundle()->setSection($label, $this->getRequest()->request->all());
            $this->notifyUser(self::NOTIFY_SUCCESS, 'Section saved.');
        } catch (\Exception $ex) {
            $this->notifyUser(self::NOTIFY_ERROR, 'Section not saved: ' . $ex->getMessage());
        }

        return $this->indexAction();
    }
}
