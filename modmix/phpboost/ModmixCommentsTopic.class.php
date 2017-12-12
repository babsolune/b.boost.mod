<?php
/*##################################################
 *                    ModmixCommentsTopic.class.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

class ModmixCommentsTopic extends CommentsTopic
{
	private $itemmix;

	public function __construct(Itemmix $itemmix = null)
	{
		parent::__construct('modmix');
		$this->itemmix = $itemmix;
	}

	public function get_authorizations()
	{
		$authorizations = new CommentsAuthorizations();
		$authorizations->set_authorized_access_module(ModmixAuthorizationsService::check_authorizations($this->get_itemmix()->get_category_id())->read());
		return $authorizations;
	}

	public function is_display()
	{
		return $this->get_itemmix()->is_published();
	}

	private function get_itemmix()
	{
		if ($this->itemmix === null)
		{
			$this->itemmix = ModmixService::get_itemmix('WHERE modmix.id=:id', array('id' => $this->get_id_in_module()));
		}
		return $this->itemmix;
	}
}
?>
