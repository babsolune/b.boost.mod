<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmConfig extends AbstractConfigData
{
	const LEFT_COLUMN_DISABLED  = 'left_column_disabled';
	const RIGHT_COLUMN_DISABLED = 'right_column_disabled';
	const PROMOTION_COLOR       = 'promotion_color';
	const PLAYOFF_PROM_COLOR    = 'playoff_prom_color';
	const PLAYOFF_RELEG_COLOR   = 'playoff_releg_color';
	const RELEGATION_COLOR      = 'relegation_color';
	const CURRENT_GAMES         = 'current_games';
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

	public function get_promotion_color()
	{
		return $this->get_property(self::PROMOTION_COLOR);
	}

	public function set_promotion_color($promotion_color)
	{
		$this->set_property(self::PROMOTION_COLOR, $promotion_color);
	}

	public function get_playoff_prom_color()
	{
		return $this->get_property(self::PLAYOFF_PROM_COLOR);
	}

	public function set_playoff_prom_color($playoff_prom_color)
	{
		$this->set_property(self::PLAYOFF_PROM_COLOR, $playoff_prom_color);
	}

	public function get_playoff_releg_color()
	{
		return $this->get_property(self::PLAYOFF_RELEG_COLOR);
	}

	public function set_playoff_releg_color($playoff_releg_color)
	{
		$this->set_property(self::PLAYOFF_RELEG_COLOR, $playoff_releg_color);
	}

	public function get_relegation_color()
	{
		return $this->get_property(self::RELEGATION_COLOR);
	}

	public function set_relegation_color($relegation_color)
	{
		$this->set_property(self::RELEGATION_COLOR, $relegation_color);
	}

	public function get_current_games()
	{
		return $this->get_property(self::CURRENT_GAMES);
	}

	public function set_current_games($current_games)
	{
		$this->set_property(self::CURRENT_GAMES, $current_games);
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
			self::PROMOTION_COLOR       => '#baffb0',
			self::PLAYOFF_PROM_COLOR    => '#b0e1ff',
			self::PLAYOFF_RELEG_COLOR   => '#feebbc',
			self::RELEGATION_COLOR      => '#deddda',
			self::CURRENT_GAMES       => false,
			self::AUTHORIZATIONS        => ['r-1' => 1, 'r0' => 5, 'r1' => 29],
			self::DEFERRED_OPERATIONS   => []
        ];
	}

	/**
	 * Returns the configuration.
	 * @return ScmConfig
	 */
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'scm', 'config');
	}

	/**
	 * Saves the configuration in the database. Has it become persistent.
	 */
	public static function save()
	{
		ConfigManager::save('scm', self::load(), 'config');
	}
}
?>
