<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class GuideSearchable extends DefaultSearchable
{
	public function __construct()
	{
		$module_id = 'guide';
		parent::__construct($module_id);
		$this->read_authorization = GuideAuthorizationsService::check_authorizations()->read();

		$this->table_name = GuideSetup::$guide_table;

		$this->authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, GuideConfig::load()->is_summary_displayed_to_guests(), $module_id);

		$this->use_keywords = true;

		$this->field_title = 'title';
		$this->field_rewrited_title = 'rewrited_title';
		$this->field_content = 'content';

		$this->has_summary = true;
		$this->field_summary = 'summary';

		$this->field_published = 'published';

		$this->has_validation_period = true;
		$this->field_validation_start_date = 'publishing_start_date';
		$this->field_validation_end_date = 'publishing_end_date';
	}
}
?>
