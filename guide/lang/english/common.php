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
$lang['guide.module.title'] = 'Guides';
$lang['guide.menu.title']     = 'Guide tree';
$lang['guide.explorer']       = 'Explorer';

// TreeLinks
$lang['items']              = 'sheets';
$lang['item']               = 'sheet';
$lang['items.reorder']      = 'Reorder sheets';
$lang['items.reordering']   = 'Reorganization of sheets';

// Table of contents
$lang['guide.contents.table']          = 'Table of contents';
$lang['guide.name']                    = 'Guide name';
$lang['guide.sticky.contents.table']   = 'Display the table of contents in a fixed position';

// Titles
$lang['guide.root']              = 'No categories';
$lang['guide.add.item']          = 'Add new sheet';
$lang['guide.edit.item']         = 'Edit sheet';
$lang['guide.my.items']          = 'My sheets';
$lang['guide.my.tracked']        = 'My favorites';
$lang['guide.member.items']      = 'Sheets published by';
$lang['guide.pending.items']     = 'Pending sheets';
$lang['guide.filter.items']      = 'Filter sheets';
$lang['guide.restore.item']      = 'Restore this version';
$lang['guide.confirm.restore']   = 'Do you really want to restore this version ?';
$lang['guide.history.init']      = 'Initialization';
$lang['guide.current.version']   = 'Current version';
$lang['guide.delete.version']    = 'Delete this version';
$lang['guide.archive']           = 'Archive';
$lang['guide.archived.item']     = 'Consult';
$lang['guide.archived.content']  = 'This sheet has been updated, your are watching an archive !';
$lang['guide.track']             = 'Follow this sheet';
$lang['guide.untrack']           = 'Unfollow this sheet';

// Levels
$lang['guide.level'] = 'Trust level';

$lang['guide.level.trust']  = 'Trusted content';
$lang['guide.level.claim']  = 'Disputed content';
$lang['guide.level.redo']   = 'Content to redo';
$lang['guide.level.sketch'] = 'Incomplete content';
$lang['guide.level.wip']    = 'Content under construction';

$lang['guide.level.trust.message']  = 'This sheet is of high quality, it is complete and reliable.';
$lang['guide.level.claim.message']  = 'This sheet has been discussed and its content does not seem correct. You can possibly consult the discussions on this subject and perhaps bring your knowledge to it.';
$lang['guide.level.redo.message']   = 'This file is to be redone, its content is not very reliable.';
$lang['guide.level.sketch.message'] = 'This sheet lacks sources.<br />Your knowledge is welcome in order to complete it.';
$lang['guide.level.wip.message']    = 'This sheet is under construction, modifications are in progress, do not hesitate to come back to consult it later';

$lang['guide.level.custom']           = 'Custom level';
$lang['guide.level.custom.content']   = 'Description of the custom level';

// Form
$lang['guide.change.reason']        = 'Type of modification';
$lang['guide.suggestions.number']   = 'Number of suggested items to display';
$lang['guide.homepage']             = 'Homepage type';
$lang['guide.homepage.categories']  = 'Categories';
$lang['guide.homepage.explorer']    = 'Explorer';

// Authorizations
$lang['guide.config.manage.history'] = 'Manage history permissions';

// SEO
$lang['guide.seo.description.root']    = 'All :site\'s guide sheets.';
$lang['guide.seo.description.tag']     = 'All guides on :subject.';
$lang['guide.seo.description.pending'] = 'All pending guides.';
$lang['guide.seo.description.member']  = 'All :author\'s guides.';
$lang['guide.seo.description.tracked']  = 'All tracked sheets for :author.';
$lang['guide.seo.description.history'] = 'History of the sheet :item.';

// Messages helper
$lang['guide.message.success.add']      = 'The sheet <b>:title</b> has been added';
$lang['guide.message.success.edit']     = 'The sheet <b>:title</b> has been modified';
$lang['guide.message.success.delete']   = 'The sheet <b>:title</b> has been deleted';
$lang['guide.message.success.delete.content'] = 'The content :content of the sheet <b>:title</b> has been deleted';
$lang['guide.message.success.restore']        = 'The content :content of the sheet <b>:title</b> has been deleted';
$lang['guide.message.draft']            = '
    <div class="message-helper bgc warning">
    Editing a file automatically places it in <b>draft</b>. This allows several validations without excessively multiplying the archives.
        <br /><br />
        <p>Remember to change the publication status at the end of the work!</p>
    </div>
';
?>
