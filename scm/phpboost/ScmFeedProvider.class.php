<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmFeedProvider implements FeedProvider
{
	public function get_feeds_list()
	{
		return CategoriesService::get_categories_manager('scm')->get_feeds_categories_module()->get_feed_list();
	}

	public function get_feed_data_struct($idcat = 0, $name = '')
	{
		$module_id = 'scm';
		if (CategoriesService::get_categories_manager($module_id)->get_categories_cache()->category_exists($idcat))
		{
			$querier = PersistenceContext::get_querier();
			$category = CategoriesService::get_categories_manager($module_id)->get_categories_cache()->get_category($idcat);

			$site_name = GeneralConfig::load()->get_site_name();
			$site_name = $idcat != Category::ROOT_CATEGORY ? $site_name . ' : ' . $category->get_name() : $site_name;

			$feed_module_name = LangLoader::get_message('scm.module.title', 'common', 'scm');
			$data = new FeedData();
			$data->set_title($feed_module_name . ' - ' . $site_name);
			$data->set_date(new Date());
			$data->set_link(SyndicationUrlBuilder::rss('scm', $idcat));
			$data->set_host(HOST);
			$data->set_desc($feed_module_name . ' - ' . $site_name);
			$data->set_lang(LangLoader::get_message('common.xml.lang', 'common-lang'));
			$data->set_auth_bit(Category::READ_AUTHORIZATIONS);

			$categories = CategoriesService::get_categories_manager($module_id)->get_children($idcat, new SearchCategoryChildrensOptions(), true);
			$ids_categories = array_keys($categories);

			$now = new Date();
			$results = $querier->select('SELECT scm.id, scm.id_category, scm.event_slug, scm.update_date, cat.rewrited_name AS rewrited_name_cat, div.division_name
				FROM ' . ScmSetup::$scm_event_table . ' scm
				LEFT JOIN '. ScmSetup::$scm_cats_table .' cat ON cat.id = scm.id_category
				LEFT JOIN '. ScmSetup::$scm_division_table .' div ON div.id_division = scm.division_id
				LEFT JOIN '. ScmSetup::$scm_season_table .' seasion ON seasion.id_season = scm.season_id
				WHERE scm.id_category IN :ids_categories
				AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
				ORDER BY scm.update_date DESC', [
					'ids_categories' => $ids_categories,
					'timestamp_now' => $now->get_timestamp()
			]);

			foreach ($results as $row)
			{
				$row['rewrited_name_cat'] = !empty($row['id_category']) ? $row['rewrited_name_cat'] : 'root';
				$link = ScmUrlBuilder::event_home($row['id'], $row['event_slug']);

				$item = new FeedItem();
				$item->set_title($row['division_name'] . ' - ' . $row['season_name']);
				$item->set_link($link);
				$item->set_guid($link);
				$item->set_date(new Date($row['update_date'], Timezone::SERVER_TIMEZONE));
				$item->set_auth(CategoriesService::get_categories_manager($module_id)->get_heritated_authorizations($row['id_category'], Category::READ_AUTHORIZATIONS, Authorizations::AUTH_PARENT_PRIORITY));
				$data->add_item($item);
			}
			$results->dispose();

			return $data;
		}
	}
}
?>
