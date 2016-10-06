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

namespace LpDigital\Bundle\ConfigurationBundle\Tests\Mock;

use LpDigital\Bundle\ConfigurationBundle\Controller\AdminController;

/**
 * Mock AdminController for test suite.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 */
class MockAdminController extends AdminController
{

    /**
     * Resets the array of notifications for this controller.
     */
    public function resetNotifications()
    {
        $this->notifications = [];
    }

    /**
     * Returns the array of notifications for this controller.
     *
     * @return array
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}
