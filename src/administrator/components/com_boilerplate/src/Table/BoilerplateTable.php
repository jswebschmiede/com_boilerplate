<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class BoilerplateTable extends Table
{
	/**
	 * @param DatabaseDriver $db A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__boilerplate_boilerplate', 'id', $db);
	}
}
