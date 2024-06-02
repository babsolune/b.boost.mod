<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballConfig extends AbstractConfigData
{
	const LEFT_COLUMN_DISABLED  = 'left_column_disabled';
	const RIGHT_COLUMN_DISABLED = 'right_column_disabled';
	const AUTHORIZATIONS        = 'authorizations';

	const DEFERRED_OPERATIONS = 'deferred_operations';

	public function disable_left_column()
	{
		$this->set_property(self::LEFT_COLUMN_DISABLED, true);
	}

	public function enable_left_column()
	{
		$this->set_property(self::LEFT_COLUMN_DISABLED, false);
	}

	public function is_left_column_disabled()
	{
		return $this->get_property(self::LEFT_COLUMN_DISABLED);
	}

	public function disable_right_column()
	{
		$this->set_property(self::RIGHT_COLUMN_DISABLED, true);
	}

	public function enable_right_column()
	{
		$this->set_property(self::RIGHT_COLUMN_DISABLED, false);
	}

	public function is_right_column_disabled()
	{
		return $this->get_property(self::RIGHT_COLUMN_DISABLED);
	}

	public function get_authorizations()
	{
		return $this->get_property(self::AUTHORIZATIONS);
	}

	public function set_authorizations(Array $authorizations)
	{
		$this->set_property(self::AUTHORIZATIONS, $authorizations);
	}

	public function get_deferred_operations()
	{
		return $this->get_property(self::DEFERRED_OPERATIONS);
	}

	public function set_deferred_operations(Array $deferred_operations)
	{
		$this->set_property(self::DEFERRED_OPERATIONS, $deferred_operations);
	}

	public function is_googlemaps_available()
	{
		return ModulesManager::is_module_installed('GoogleMaps') && ModulesManager::is_module_activated('GoogleMaps') && GoogleMapsConfig::load()->get_api_key();
	}

	public function get_default_values()
	{
		return [
			self::LEFT_COLUMN_DISABLED  => false,
			self::RIGHT_COLUMN_DISABLED => false,
			self::AUTHORIZATIONS        => ['r-1' => 1, 'r0' => 5, 'r1' => 29],
			self::DEFERRED_OPERATIONS   => []
        ];
	}

	/**
	 * Returns the configuration.
	 * @return FootballConfig
	 */
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'football', 'config');
	}

	/**
	 * Saves the configuration in the database. Has it become persistent.
	 */
	public static function save()
	{
		ConfigManager::save('football', self::load(), 'config');
	}
}
?>
