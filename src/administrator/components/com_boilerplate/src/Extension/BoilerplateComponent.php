<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Tag\TagServiceTrait;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Tag\TagServiceInterface;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Component\Router\RouterServiceInterface;

/**
 * Component class for com_boilerplate
 *
 * @since  4.0.0
 */
class BoilerplateComponent extends MVCComponent implements
	BootableExtensionInterface,
	CategoryServiceInterface,
	TagServiceInterface,
	RouterServiceInterface
{
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;
	use CategoryServiceTrait, TagServiceTrait {
		CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
		CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
	}

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function boot(ContainerInterface $container): void
	{

	}

	/**
	 * Returns the table for the count items functions for the given section.
	 *
	 * @param   ?string  $section  The section
	 *
	 * @return  string|null
	 *
	 * @since   4.0.0
	 */
	protected function getTableNameForSection(string $section = null)
	{
		return 'boilerplate_boilerplate';
	}
}
