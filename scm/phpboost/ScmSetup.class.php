<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSetup extends DefaultModuleSetup
{
	public static $scm_cats_table;
	public static $scm_event_table;
	public static $scm_club_table;
	public static $scm_day_table;
	public static $scm_division_table;
	public static $scm_game_table;
	public static $scm_params_table;
	// public static $scm_ranking_table;
	// public static $scm_result_table;
	public static $scm_season_table;
	public static $scm_team_table;

	public static function __static()
	{
		self::$scm_cats_table = PREFIX . 'scm_cats';
		self::$scm_event_table = PREFIX . 'scm';
		self::$scm_club_table = PREFIX . 'scm_club';
		self::$scm_day_table = PREFIX . 'scm_day';
		self::$scm_division_table = PREFIX . 'scm_division';
		self::$scm_params_table = PREFIX . 'scm_params';
		self::$scm_game_table = PREFIX . 'scm_game';
		// self::$scm_ranking_table = PREFIX . 'scm_ranking';
		// self::$scm_result_table = PREFIX . 'scm_result';
		self::$scm_season_table = PREFIX . 'scm_season';
		self::$scm_team_table = PREFIX . 'scm_team';
	}

	public function install()
	{
		$this->drop_tables();
		$this->create_tables();
		$this->insert_data();
	}

	public function uninstall()
	{
		$this->drop_tables();
		ConfigManager::delete('scm', 'config');
		CacheManager::invalidate('module', 'scm');
		KeywordsService::get_keywords_manager()->delete_module_relations();
	}

	private function drop_tables()
	{
		PersistenceContext::get_dbms_utils()->drop(array(
			self::$scm_cats_table,
			self::$scm_event_table,
			self::$scm_club_table,
			self::$scm_day_table,
			self::$scm_division_table,
			self::$scm_game_table,
			self::$scm_params_table,
			// self::$scm_ranking_table,
			// self::$scm_result_table,
			self::$scm_season_table,
			self::$scm_team_table
		));
	}

	private function create_tables()
	{
		$this->create_scm_cats_table();
		$this->create_scm_event_table();
		$this->create_scm_club_table();
		$this->create_scm_day_table();
		$this->create_scm_division_table();
		$this->create_scm_game_table();
		$this->create_scm_params_table();
		// $this->create_scm_ranking_table();
		// $this->create_scm_result_table();
		$this->create_scm_season_table();
		$this->create_scm_team_table();
	}

	private function create_scm_cats_table()
	{
		RichCategory::create_categories_table(self::$scm_cats_table);
	}

	private function create_scm_event_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'id_category' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'event_slug' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'season_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'division_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'start_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'end_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),

			'views_number' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'published' => array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0),
			'publishing_start_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'publishing_end_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'creation_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'update_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'sources' => array('type' => 'text', 'length' => 65000)
		);
		$options = array(
			'primary' => array('id'),
			'indexes' => array(
				'id_category' => array('type' => 'key', 'fields' => 'id_category'),
				'event_slug' => array('type' => 'fulltext', 'fields' => 'event_slug')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_event_table, $fields, $options);
	}

	private function create_scm_club_table()
	{
		$fields = array(
			'id_club'          => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'club_name'        => array('type' => 'string', 'length' => 255, 'notnull' => 0, 'default' => "''"),
			'club_slug'        => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'club_full_name'   => array('type' => 'string', 'length' => 255, 'notnull' => 0, 'default' => "''"),
			'club_logo'        => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'club_flag'        => array('type' => 'string', 'length' => 2, 'default' => "''"),
			'club_email'       => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'club_phone'       => array('type' => 'string', 'length' => 25, 'default' => "''"),
			'club_colors'      => array('type' => 'text', 'length' => 65000),
			'club_locations'   => array('type' => 'text', 'length' => 65000),
			'club_map_display' => array('type' => 'boolean', 'default' => 0),
		);
		$options = array(
			'primary' => array('id_club'),
			'indexes' => array(
				'club_name' => array('type' => 'fulltext', 'fields' => 'club_name')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_club_table, $fields, $options);
	}

	private function create_scm_day_table()
	{
		$fields = array(
			'id_day' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'day_event_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1),
			'day_round' => array('type' => 'integer', 'length' => 11, 'notnull' => 1),
			'day_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1),
			'day_played' => array('type' => 'boolean', 'default' => 0),
		);
		$options = array(
			'primary' => array('id_day'),
			'indexes' => array(
				'day_event_id' => array('type' => 'key', 'fields' => 'day_event_id')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_day_table, $fields, $options);
	}

	private function create_scm_division_table()
	{
		$fields = array(
			'id_division' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'division_name' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'event_type' => array('type' => 'string', 'length' => 255, 'notnull' => 1),
			'game_type' => array('type' => 'string', 'length' => 255, 'notnull' => 1),
		);
		$options = array(
			'primary' => array('id_division'),
			'indexes' => array(
				'division_name' => array('type' => 'fulltext', 'fields' => 'division_name')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_division_table, $fields, $options);
	}

	private function create_scm_game_table()
	{
		$fields = array(
			'id_game' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'game_event_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1),
			'game_playground' => array('type' => 'string', 'length' => 65),
			'game_type' => array('type' => 'string', 'length' => 11),
			'game_group' => array('type' => 'integer', 'length' => 11),
			'game_order' => array('type' => 'integer', 'length' => 11),
			'game_day' => array('type' => 'integer', 'length' => 11),
			'game_home_id' => array('type' => 'integer', 'length' => 11),
			'game_home_score' => array('type' => 'string', 'length' => 2),
			'game_home_pen' => array('type' => 'string', 'length' => 2),
			'game_away_pen' => array('type' => 'string', 'length' => 2),
			'game_away_score' => array('type' => 'string', 'length' => 2),
			'game_away_id' => array('type' => 'integer', 'length' => 11),
			'game_date' => array('type' => 'integer', 'length' => 11),
		);
		$options = array(
			'primary' => array('id_game'),
			'indexes' => array(
				'game_event_id' => array('type' => 'key', 'fields' => 'game_event_id')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_game_table, $fields, $options);
	}

	private function create_scm_params_table()
	{
		$fields = array(
			'id_params' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'params_event_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1),

			'teams_per_group' => array('type' => 'integer', 'length' => 11),
			'group_number' => array('type' => 'integer', 'length' => 11),
			'hat_ranking' => array('type' => 'boolean', 'default' => 0),
			'hat_days' => array('type' => 'integer', 'length' => 11),
			'fill_games' => array('type' => 'boolean', 'default' => 0),
			'looser_bracket' => array('type' => 'boolean', 'default' => 0),
			'display_playgrounds' => array('type' => 'boolean', 'default' => 0),

			'rounds_number' => array('type' => 'integer', 'length' => 11, 'not'),
			'draw_games' => array('type' => 'boolean', 'default' => 0),
			'has_overtime' => array('type' => 'boolean', 'default' => 0),
			'overtime_duration' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'third_place' => array('type' => 'boolean', 'default' => 0),
			'golden_goal' => array('type' => 'boolean', 'default' => 0),
			'silver_goal' => array('type' => 'boolean', 'default' => 0),

			'victory_points' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'draw_points' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'loss_points' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'promotion' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'playoff' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'relegation' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'ranking_type' => array('type' => 'string', 'length' => 255, 'default' => "''"),

			'game_duration' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'favorite_team_id' => array('type' => 'integer', 'length' => 11, 'default' => 0)
		);
		$options = array(
			'primary' => array('id_params'),
			'indexes' => array(
				'params_event_id' => array('type' => 'key', 'fields' => 'params_event_id')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_params_table, $fields, $options);
	}

	// private function create_scm_ranking_table()
	// {
	// 	$fields = array(
	// 		'id_ranking' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
	// 		'ranking_event_id' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_group_id' => array('type' => 'string', 'length' => 11, 'default' => 0),
	// 		'ranking_rank' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_team_id' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_points' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_played' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_win' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_draw' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_loss' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_goals_for' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'ranking_goals_against' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 	);
	// 	$options = array(
	// 		'primary' => array('id_ranking'),
	// 		'indexes' => array(
	// 			'ranking_event_id' => array('type' => 'key', 'fields' => 'ranking_event_id')
	// 		)
	// 	);
	// 	PersistenceContext::get_dbms_utils()->create_table(self::$scm_ranking_table, $fields, $options);
	// }

	// private function create_scm_result_table()
	// {
	// 	$fields = array(
	// 		'id_result' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
	// 		'result_event_id' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'result_game_id' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'result_group_id' => array('type' => 'string', 'length' => 11, 'default' => 0),
	// 		'result_team_id' => array('type' => 'integer', 'length' => 11, 'default' => 0),
	// 		'result_goals_for' => array('type' => 'string', 'length' => 3, 'default' => 0),
	// 		'result_goals_against' => array('type' => 'string', 'length' => 3, 'default' => 0),
	// 	);
	// 	$options = array(
	// 		'primary' => array('id_result'),
	// 		'indexes' => array(
	// 			'result_event_id' => array('type' => 'key', 'fields' => 'result_event_id')
	// 		)
	// 	);
	// 	PersistenceContext::get_dbms_utils()->create_table(self::$scm_result_table, $fields, $options);
	// }

	private function create_scm_season_table()
	{
		$fields = array(
			'id_season' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'season_name' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'first_year' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'calendar_year' => array('type' => 'boolean', 'default' => 0),
		);
		$options = array(
			'primary' => array('id_season'),
			'indexes' => array(
				'season_name' => array('type' => 'fulltext', 'fields' => 'season_name')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_season_table, $fields, $options);
	}

	private function create_scm_team_table()
	{
		$fields = array(
			'id_team' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'team_event_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'team_group' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'team_order' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'team_club_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'team_penalty' => array('type' => 'integer', 'length' => 11),
		);
		$options = array(
			'primary' => array('id_team'),
			'indexes' => array(
				'team_event_id' => array('type' => 'key', 'fields' => 'team_event_id')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$scm_team_table, $fields, $options);
	}

	private function insert_data()
	{
		$categories_file = PATH_TO_ROOT . '/scm/data/' . AppContext::get_current_user()->get_locale() . '/categories.csv';
        $row = 1;
        if (($handle = fopen($categories_file, 'r')) !== FALSE)
        {
            while(($data = fgetcsv($handle, 1000, '|')) !== FALSE)
            {
                if($row == 1){ $row++; continue; }
                PersistenceContext::get_querier()->insert(self::$scm_cats_table, array(
                    'id'     				 => null,
                    'name'        			 => $data[0],
                    'rewrited_name'     	 => $data[1],
                    'c_order'  				 => $data[2],
                    'special_authorizations' => $data[3],
                    'auth'  				 => $data[4],
                    'id_parent'  			 => $data[5],
                    'description'  			 => $data[6],
                    'thumbnail'  			 => $data[7],
                ));
                $row++;
            }
            fclose($handle);
        }
        else
        {
            echo '<div class="message-helper bgc-full error">Erreur lors de l\'ouverture du fichier CSV.</div>';
        }
		$clubs_file = PATH_TO_ROOT . '/scm/data/' . AppContext::get_current_user()->get_locale() . '/clubs.csv';
        $row = 1;
        if (($handle = fopen($clubs_file, 'r')) !== FALSE)
        {
            while(($data = fgetcsv($handle, 1000, '|')) !== FALSE)
            {
                if($row == 1){ $row++; continue; }
                PersistenceContext::get_querier()->insert(self::$scm_club_table, array(
                    'id_club'          => null,
                    'club_name'        => $data[0],
                    'club_slug'        => $data[1],
                    'club_full_name'   => $data[2],
                    'club_logo'        => $data[3],
                    'club_flag'        => $data[4],
                    'club_locations'   => $data[8],
                    'club_map_display' => $data[9],
                ));
                $row++;
            }
            fclose($handle);
        }
        else
        {
            echo '<div class="message-helper bgc-full error">Erreur lors de l\'ouverture du fichier CSV.</div>';
        }
	}
}
?>
