<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballBracketService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    // Add matches
    public static function set_bracket_matches($compet_id, $rounds_number)
    {
        $now = new Date();
        $c_return_matches = FootballCompetService::get_compet_match_type($compet_id) == FootballDivision::RETURN_MATCHES;
        $c_looser_bracket = FootballParamsService::get_params($compet_id)->get_looser_bracket();
        $teams_number = FootballParamsService::get_params($compet_id)->get_teams_per_group();
        $c_hat_ranking = FootballParamsService::get_params($compet_id)->get_hat_ranking();
        $rounds_number = $c_hat_ranking ? $rounds_number + 1 : $rounds_number;

        for ($i = $rounds_number; $i >= 1; $i--)
        {
            if ($c_hat_ranking && $i == $rounds_number && $c_return_matches)
                $matches_number = FootballParamsService::get_params($compet_id)->get_play_off() / 2;
            else
                $matches_number = $c_looser_bracket ? $teams_number : self::round_matches_number($i, $c_return_matches);

            for ($j = 1; $j <= $matches_number; $j++)
            {
                if ($c_looser_bracket)
                    self::$db_querier->insert(FootballSetup::$football_match_table, array(
                        'match_compet_id' => $compet_id,
                        'match_type' => 'L',
                        'match_group' => $i,
                        'match_order' => $j,
                        'match_home_id' => 0,
                        'match_away_id' => 0,
                        'match_date' => $now->get_timestamp()
                    ));
                self::$db_querier->insert(FootballSetup::$football_match_table, array(
                    'match_compet_id' => $compet_id,
                    'match_type' => 'W',
                    'match_group' => $i,
                    'match_order' => $j,
                    'match_home_id' => 0,
                    'match_away_id' => 0,
                    'match_date' => $now->get_timestamp()
                ));
            }
        }
    }

    // Doc of matches
    public static function get_bracket_js_matches($compet_id, $teams_number, $teams_per_group)
    {
        $view = new FileTemplate('football/js/bracket-matches.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $view->put_all(array(
            'C_'.$teams_number.'_'.$teams_per_group => true,
            'C_LOOSER_BRACKET' => FootballParamsService::get_params($compet_id)->get_looser_bracket(),
            'C_THIRD_PLACE' => FootballParamsService::get_params($compet_id)->get_third_place()
        ));
        return $view;
    }

    public static function round_matches_number(int $rounds, bool $c_return_matches)
    {
        if($c_return_matches) {
            $array = [1 => 1, 2 => 4, 3 => 8, 4 => 16, 5 => 32, 6 => 64, 7 => 128];
        } else {
            $array = [1 => 1, 2 => 2, 3 => 4, 4 => 8, 5 => 16, 6 => 32, 7 => 64];
        }
        if (array_key_exists($rounds, $array)) {
            return $array[$rounds];
        } else {
            return null;
        }
    }
}