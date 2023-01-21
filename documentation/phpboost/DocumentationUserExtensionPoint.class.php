<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 22
 * @since       PHPBoost 6.0 - 2022 11 22
*/

class DocumentationUserExtensionPoint implements UserExtensionPoint
{
	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_view($user_id)
	{
		return DocumentationUrlBuilder::display_member_items($user_id)->rel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_name()
	{
		return LangLoader::get_message('documentation.module.title', 'common', 'documentation');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_id()
	{
		return 'documentation';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_module_icon()
	{
		return 'fa fa-fw fa-book-atlas';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_publications_number($user_id)
	{
		return PersistenceContext::get_querier()->count(PREFIX . 'documentation_contents', 'WHERE author_user_id = :user_id', array('user_id' => $user_id));
	}
}
