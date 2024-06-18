<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballConfig extends AbstractConfigData
{
	const LEFT_COLUMN_DISABLED  = 'left_column_disabled';
	const RIGHT_COLUMN_DISABLED = 'right_column_disabled';
	const PROMOTION_COLOR       = 'promotion_color';
	const PLAYOFF_COLOR         = 'playoff_color';
	const RELEGATION_COLOR      = 'relegation_color';
	const LIVE_COLOR            = 'live_color';
	const PLAYED_COLOR          = 'played_color';
	const WIN_COLOR             = 'win_color';
	const CURRENT_MATCHES       = 'current_matches';
	const NEXT_MATCHES          = 'next_matches';
	const NEXT_MATCHES_NUMBER   = 'next_matches_number';
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

	public function get_playoff_color()
	{
		return $this->get_property(self::PLAYOFF_COLOR);
	}

	public function set_playoff_color($playoff_color)
	{
		$this->set_property(self::PLAYOFF_COLOR, $playoff_color);
	}

	public function get_relegation_color()
	{
		return $this->get_property(self::RELEGATION_COLOR);
	}

	public function set_relegation_color($relegation_color)
	{
		$this->set_property(self::RELEGATION_COLOR, $relegation_color);
	}

	public function get_live_color()
	{
		return $this->get_property(self::LIVE_COLOR);
	}

	public function set_live_color($live_color)
	{
		$this->set_property(self::LIVE_COLOR, $live_color);
	}

	public function get_played_color()
	{
		return $this->get_property(self::PLAYED_COLOR);
	}

	public function set_played_color($played_color)
	{
		$this->set_property(self::PLAYED_COLOR, $played_color);
	}

	public function get_win_color()
	{
		return $this->get_property(self::WIN_COLOR);
	}

	public function set_win_color($win_color)
	{
		$this->set_property(self::WIN_COLOR, $win_color);
	}

	public function get_current_matches()
	{
		return $this->get_property(self::CURRENT_MATCHES);
	}

	public function set_current_matches($current_matches)
	{
		$this->set_property(self::CURRENT_MATCHES, $current_matches);
	}

	public function get_next_matches()
	{
		return $this->get_property(self::NEXT_MATCHES);
	}

	public function set_next_matches($next_matches)
	{
		$this->set_property(self::NEXT_MATCHES, $next_matches);
	}

	public function get_next_matches_number()
	{
		return $this->get_property(self::NEXT_MATCHES_NUMBER);
	}

	public function set_next_matches_number($next_matches_number)
	{
		$this->set_property(self::NEXT_MATCHES_NUMBER, $next_matches_number);
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
			self::PLAYOFF_COLOR         => '#b0e1ff',
			self::RELEGATION_COLOR      => '#deddda',
			self::LIVE_COLOR            => '#baffb0',
			self::PLAYED_COLOR          => '#deddda',
			self::WIN_COLOR             => '#baffb0',
			self::CURRENT_MATCHES       => false,
			self::NEXT_MATCHES          => false,
			self::NEXT_MATCHES_NUMBER   => 4,
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
