<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsCategoriesCache extends DefaultRichCategoriesCache
{
	public function get_module_identifier()
	{
		return 'clubs';
	}

	protected function get_category_elements_number($id_category)
	{
		$now = new Date();
		return ClubsService::count('WHERE id_category = :id_category AND published = 1',
			array(
				'timestamp_now' => $now->get_timestamp(),
				'id_category' => $id_category
			)
		);
	}

	protected function get_root_category_authorizations()
	{
		return ClubsConfig::load()->get_authorizations();
	}

	protected function get_root_category_description()
	{
		$description = ClubsConfig::load()->get_root_category_description();
		if (empty($description))
			$description = StringVars::replace_vars(LangLoader::get_message('clubs.seo.description.root', 'common', 'clubs'), array('site' => GeneralConfig::load()->get_site_name()));
		return $description;
	}
}
?>
