<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 30
 * @since       PHPBoost 6.0 - 2022 11 18
 */

####################################################
#                       English                    #
####################################################

// Module titles
$lang['docsheet.module.title'] = 'Documentation';
$lang['docsheet.menu.title']     = 'Documentation tree';
$lang['docsheet.explorer']       = 'Explorer';

// TreeLinks
$lang['item']               = 'sheet';
$lang['items']              = 'sheets';
$lang['items.reorder']      = 'Reorder sheets';
$lang['items.reordering']   = 'Reorganization of sheets';

// Table of contents
$lang['docsheet.contents.table']          = 'Table of contents';
$lang['docsheet.name']                    = 'Documentation name';
$lang['docsheet.sticky.contents.table']   = 'Display the table of contents in a fixed position';

// Titles
$lang['docsheet.root']              = 'No categories';
$lang['docsheet.add.item']          = 'Add new sheet';
$lang['docsheet.edit.item']         = 'Edit sheet';
$lang['docsheet.my.items']          = 'My sheets';
$lang['docsheet.my.tracked']        = 'My favorites';
$lang['docsheet.member.items']      = 'Sheets published by';
$lang['docsheet.pending.items']     = 'Pending sheets';
$lang['docsheet.filter.items']      = 'Filter sheets';
$lang['docsheet.restore.item']      = 'Restore this version';
$lang['docsheet.confirm.restore']   = 'Do you really want to restore this version ?';
$lang['docsheet.history.init']      = 'Initialization';
$lang['docsheet.current.version']   = 'Current version';
$lang['docsheet.delete.version']    = 'Delete this version';
$lang['docsheet.archive']           = 'Archive';
$lang['docsheet.archived.item']     = 'Consult';
$lang['docsheet.archived.content']  = 'This sheet has been updated, your are watching an archive !';
$lang['docsheet.track']             = 'Follow this sheet';
$lang['docsheet.untrack']           = 'Unfollow this sheet';

// Levels
$lang['docsheet.level'] = 'Trust level';

$lang['docsheet.level.trust']  = 'Trusted content';
$lang['docsheet.level.claim']  = 'Disputed content';
$lang['docsheet.level.redo']   = 'Content to redo';
$lang['docsheet.level.sketch'] = 'Incomplete content';
$lang['docsheet.level.wip']    = 'Content under construction';

$lang['docsheet.level.trust.message']  = 'This sheet is of high quality, it is complete and reliable.';
$lang['docsheet.level.claim.message']  = 'This sheet has been discussed and its content does not seem correct. You can possibly consult the discussions on this subject and perhaps bring your knowledge to it.';
$lang['docsheet.level.redo.message']   = 'This file is to be redone, its content is not very reliable.';
$lang['docsheet.level.sketch.message'] = 'This sheet lacks sources.<br />Your knowledge is welcome in order to complete it.';
$lang['docsheet.level.wip.message']    = 'This sheet is under construction, modifications are in progress, do not hesitate to come back to consult it later';

$lang['docsheet.level.custom']           = 'Custom level';
$lang['docsheet.level.custom.content']   = 'Description of the custom level';

// Form
$lang['docsheet.change.reason']        = 'Type of modification';
$lang['docsheet.suggestions.number']   = 'Number of suggested items to display';
$lang['docsheet.homepage']             = 'Homepage type';
$lang['docsheet.homepage.categories']  = 'Categories';
$lang['docsheet.homepage.explorer']    = 'Explorer';

// Authorizations
$lang['docsheet.config.manage.history'] = 'Manage history permissions';

// SEO
$lang['docsheet.seo.description.root']    = 'All :site\'s sheets.';
$lang['docsheet.seo.description.tag']     = 'All sheets on :subject.';
$lang['docsheet.seo.description.pending'] = 'All pending sheets.';
$lang['docsheet.seo.description.member']  = 'All :author\'s sheets.';
$lang['docsheet.seo.description.tracked']  = 'All tracked sheets for :author.';
$lang['docsheet.seo.description.history'] = 'History of the sheet :item.';

// Messages helper
$lang['docsheet.message.success.add']      = 'The sheet <b>:title</b> has been added';
$lang['docsheet.message.success.edit']     = 'The sheet <b>:title</b> has been modified';
$lang['docsheet.message.success.delete']   = 'The sheet <b>:title</b> has been deleted';
$lang['docsheet.message.success.delete.content'] = 'The content :content of the sheet <b>:title</b> has been deleted';
$lang['docsheet.message.success.restore']        = 'The content :content of the sheet <b>:title</b> has been deleted';
$lang['docsheet.message.draft']            = '
    <div class="message-helper bgc warning">
    Editing a file automatically places it in <b>draft</b>. This allows several validations without excessively multiplying the archives.
        <br /><br />
        <p>Remember to change the publication status at the end of the work!</p>
    </div>
';
?>
