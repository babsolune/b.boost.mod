<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 22
 * @since       PHPBoost 6.0 - 2022 11 22
*/

class DocsheetUserExtensionPoint implements UserExtensionPoint
{
	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_view($user_id)
	{
		return DocsheetUrlBuilder::display_member_items($user_id)->rel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_name()
	{
		return LangLoader::get_message('docsheet.module.title', 'common', 'docsheet');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_id()
	{
		return 'docsheet';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_icon()
	{
		return 'fa fa-fw fa-graduation-cap';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_number($user_id)
	{
		return PersistenceContext::get_querier()->count(PREFIX . 'docsheet_contents', 'WHERE author_user_id = :user_id AND active_content = 1', array('user_id' => $user_id));
	}
}
