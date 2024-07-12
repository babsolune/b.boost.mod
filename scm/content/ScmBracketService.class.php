<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 08 10
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmBracketService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** number to letter */
	public static function ntl(int $number) : string
    {
        return chr(63 + $number);
    }

    // Add games
    public static function set_bracket_games($event_id, $rounds_number)
    {
        $c_return_games   = ScmEventService::get_event_game_type($event_id) == ScmDivision::RETURN_GAMES;
        $c_looser_bracket = ScmParamsService::get_params($event_id)->get_looser_bracket();
        $c_third_place    = ScmParamsService::get_params($event_id)->get_third_place();
        $c_hat_ranking    = ScmParamsService::get_params($event_id)->get_hat_ranking();
        $rounds_number    = $c_hat_ranking ? $rounds_number + 1 : $rounds_number;
        $brackets_number  = $c_looser_bracket ? ScmParamsService::get_params($event_id)->get_brackets_number() + 1 : 1;

        for ($b = $brackets_number; $b >= 1 ; $b--)
        {
            for ($i = $rounds_number; $i >= 1; $i--)
            {
                if ($c_hat_ranking && $i == $rounds_number && $c_return_games)
                    $games_number = ScmParamsService::get_params($event_id)->get_playoff();
                elseif ($c_hat_ranking && $i == $rounds_number && !$c_return_games)
                    $games_number = ScmParamsService::get_params($event_id)->get_playoff() / 2;
                elseif ($c_looser_bracket)
                    $games_number = self::round_games_number($rounds_number, $c_return_games);
                else
                    $games_number = self::round_games_number($i, $c_return_games, $c_third_place);

                for ($j = 1; $j <= $games_number; $j++)
                {
                    self::$db_querier->insert(ScmSetup::$scm_game_table, [
                        'game_event_id' => $event_id,
                        'game_type'     => 'B',
                        'game_group'    => $i,
                        'game_round'    => $b,
                        'game_order'    => $j,
                        'game_home_id'  => 0,
                        'game_away_id'  => 0,
                        'game_date'     => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
                    ]);
                }
            }
        }
    }

    // Doc of games = fill empty games with corresponding game id
    public static function get_bracket_js_games($event_id, $teams_number, $teams_per_group)
    {
        $view = new FileTemplate('scm/js/bracket-games.tpl');
        $view->add_lang(LangLoader::get_all_langs('scm'));

        $view->put_all([
            'C_'.$teams_number.'_'.$teams_per_group => true,
            'C_LOOSER_BRACKET' => ScmParamsService::get_params($event_id)->get_looser_bracket(),
            'C_THIRD_PLACE'    => ScmParamsService::get_params($event_id)->get_third_place()
        ]);
        return $view;
    }

    public static function round_games_number(int $rounds, bool $c_return_games, bool $c_third_place = false)
    {
        $final = $c_third_place ? 2 : 1;
        if($c_return_games) {
            $array = [1 => $final, 2 => 4, 3 => 8, 4 => 16, 5 => 32, 6 => 64, 7 => 128];
        } else {
            $array = [1 => $final, 2 => 2, 3 => 4, 4 => 8, 5 => 16, 6 => 32, 7 => 64];
        }
        if (array_key_exists($rounds, $array)) {
            return $array[$rounds];
        } else {
            return null;
        }
    }
}