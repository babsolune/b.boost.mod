<?php
/*##################################################
 *		                   ModcatfullConfig.class.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

class ModcatfullConfig extends AbstractConfigData
{
	const ITEMS_NUMBER_PER_PAGE = 'items_number_per_page';
	const CATEGORIES_NUMBER_PER_PAGE = 'categories_number_per_page';
	const COLS_NUMBER_DISPLAYED_PER_LINE = 'cols_number_displayed_per_line';
	const CHARACTERS_NUMBER_TO_CUT = 'characters_number_to_cut';

	const ENABLED_CATS_ICON = 'enabled_cats_icon';
	const ENABLED_SORT_FILTERS = 'enabled_sort_filters';
	const DESCRIPTIONS_DISPLAYED_TO_GUESTS = 'descriptions_displayed_to_guests';
	const UPDATED_DATE_DISPLAYED = 'updated_date_displayed';
	const ROOT_CATEGORY_DESCRIPTION = 'root_category_description';
	const ENABLED_ITEMS_SUGGESTIONS = 'enabled_items_suggestions';
	const SUGGESTED_ITEMS_NB = 'suggested_items_nb';
	const ENABLED_NAVIGATION_LINKS = 'enabled_navigation_links';

	const DISPLAY_TYPE = 'display_type';
	const MOSAIC_DISPLAY = 'mosaic';
	const LIST_DISPLAY = 'list';
	const TABLE_DISPLAY = 'table';

	const DEFERRED_OPERATIONS = 'deferred_operations';

	const AUTHORIZATIONS = 'authorizations';

	public function get_items_number_per_page()
	{
		return $this->get_property(self::ITEMS_NUMBER_PER_PAGE);
	}

	public function set_items_number_per_page($number)
	{
		$this->set_property(self::ITEMS_NUMBER_PER_PAGE, $number);
	}

	public function get_categories_number_per_page()
	{
		return $this->get_property(self::CATEGORIES_NUMBER_PER_PAGE);
	}

	public function set_categories_number_per_page($number)
	{
		$this->set_property(self::CATEGORIES_NUMBER_PER_PAGE, $number);
	}

	public function get_cols_number_displayed_per_line()
	{
		return $this->get_property(self::COLS_NUMBER_DISPLAYED_PER_LINE);
	}

	public function set_cols_number_displayed_per_line($number)
	{
		$this->set_property(self::COLS_NUMBER_DISPLAYED_PER_LINE, $number);
	}

	public function get_characters_number_to_cut()
	{
		return $this->get_property(self::CHARACTERS_NUMBER_TO_CUT);
	}

	public function set_characters_number_to_cut($number)
	{
		$this->set_property(self::CHARACTERS_NUMBER_TO_CUT, $number);
	}

	public function get_display_type()
	{
		return $this->get_property(self::DISPLAY_TYPE);
	}

	public function set_display_type($display_type)
	{
		$this->set_property(self::DISPLAY_TYPE, $display_type);
	}

	public function display_descriptions_to_guests()
	{
		$this->set_property(self::DESCRIPTIONS_DISPLAYED_TO_GUESTS, true);
	}

	public function hide_descriptions_to_guests()
	{
		$this->set_property(self::DESCRIPTIONS_DISPLAYED_TO_GUESTS, false);
	}

	public function are_descriptions_displayed_to_guests()
	{
		return $this->get_property(self::DESCRIPTIONS_DISPLAYED_TO_GUESTS);
	}

	public function enable_cats_icon()
	{
		$this->set_property(self::ENABLED_CATS_ICON, true);
	}

	public function disable_cats_icon() {
		$this->set_property(self::ENABLED_CATS_ICON, false);
	}

	public function are_cat_icons_enabled()
	{
		return $this->get_property(self::ENABLED_CATS_ICON);
	}

	public function enable_sort_filters()
	{
		$this->set_property(self::ENABLED_SORT_FILTERS, true);
	}

	public function disable_sort_filters() {
		$this->set_property(self::ENABLED_SORT_FILTERS, false);
	}

	public function are_sort_filters_enabled()
	{
		return $this->get_property(self::ENABLED_SORT_FILTERS);
	}

	public function get_updated_date_displayed()
	{
		return $this->get_property(self::UPDATED_DATE_DISPLAYED);
	}

	public function set_updated_date_displayed($updated_date_displayed)
	{
		$this->set_property(self::UPDATED_DATE_DISPLAYED, $updated_date_displayed);
	}

	public function get_root_category_description()
	{
		return $this->get_property(self::ROOT_CATEGORY_DESCRIPTION);
	}

	public function set_root_category_description($value)
	{
		$this->set_property(self::ROOT_CATEGORY_DESCRIPTION, $value);
	}

	public function get_enabled_items_suggestions()
	{
		return $this->get_property(self::ENABLED_ITEMS_SUGGESTIONS);
	}

	public function set_enabled_items_suggestions($enabled_items_suggestions)
	{
		$this->set_property(self::ENABLED_ITEMS_SUGGESTIONS, $enabled_items_suggestions);
	}

	public function get_suggested_items_nb()
	{
		return $this->get_property(self::SUGGESTED_ITEMS_NB);
	}

	public function set_suggested_items_nb($number)
	{
		$this->set_property(self::SUGGESTED_ITEMS_NB, $number);
	}

	public function get_enabled_navigation_links()
	{
		return $this->get_property(self::ENABLED_NAVIGATION_LINKS);
	}

	public function set_enabled_navigation_links($enabled_navigation_links)
	{
		$this->set_property(self::ENABLED_NAVIGATION_LINKS, $enabled_navigation_links);
	}

	public function get_authorizations()
	{
		return $this->get_property(self::AUTHORIZATIONS);
	}

	public function set_authorizations(Array $array)
	{
		$this->set_property(self::AUTHORIZATIONS, $array);
	}

	public function get_deferred_operations()
	{
		return $this->get_property(self::DEFERRED_OPERATIONS);
	}

	public function set_deferred_operations(Array $deferred_operations)
	{
		$this->set_property(self::DEFERRED_OPERATIONS, $deferred_operations);
	}

	public function get_default_values()
	{
		return array(
			self::ITEMS_NUMBER_PER_PAGE => 10,
			self::CATEGORIES_NUMBER_PER_PAGE => 10,
			self::COLS_NUMBER_DISPLAYED_PER_LINE => 2,
			self::CHARACTERS_NUMBER_TO_CUT => 128,
			self::ENABLED_CATS_ICON => false,
			self::ENABLED_SORT_FILTERS => true,
			self::DESCRIPTIONS_DISPLAYED_TO_GUESTS => false,
			self::UPDATED_DATE_DISPLAYED => false,
			self::ENABLED_ITEMS_SUGGESTIONS => false,
			self::SUGGESTED_ITEMS_NB => 4,
			self::ENABLED_NAVIGATION_LINKS => false,
			self::DISPLAY_TYPE => self::MOSAIC_DISPLAY,
			self::ROOT_CATEGORY_DESCRIPTION => LangLoader::get_message('root_category_description', 'config', 'modcatfull'),
			self::AUTHORIZATIONS => array('r-1' => 1, 'r0' => 5, 'r1' => 13),
			self::DEFERRED_OPERATIONS => array()
		);
	}

	/**
	 * Returns the configuration.
	 * @return ModcatfullConfig
	 */
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'modcatfull', 'config');
	}

	/**
	 * Saves the configuration in the database. Has it become persistent.
	 */
	public static function save()
	{
		ConfigManager::save('modcatfull', self::load(), 'config');
	}
}
?>
