/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
*/

// Menu constructor
let title = jQuery('.content .formatter-title:not(span)');
if (title.length == 0)
{
    jQuery('#sheet-summary').remove();
}
title.each(function () {
    let rewrited = jQuery(this).text().replace(/[^a-zA-Z0-9]/ig, "-").toLowerCase();
    jQuery(this).attr('id', rewrited);
    let innerhtml = jQuery(this).html();
    let padding = '';
    let hyphen = '<span>&vdash;</span>';
    if (jQuery(this).is('h2')) {padding = '0.618em'; hyphen = '<i class="fa fa-circle smaller"></i> '}
    if (jQuery(this).is('h3')) padding = '1.618em';
    if (jQuery(this).is('h4')) padding = '2.618em';
    if (jQuery(this).is('h5')) padding = '3.618em';
    if (jQuery(this).is('h6')) padding = '4.618em';
    jQuery('#summary-list').append(jQuery('<li><a class="summary-title" href="#' + rewrited + '" style="padding-left: ' + padding + '">' + hyphen + '<span class="inner-title">' + innerhtml + '</span></a></li>'));
});

// smoth scroll
jQuery('.summary-title').on('click', function () {
    var idTarget = $(this).attr("href");

    $('html, body').animate({
        scrollTop: $(idTarget).offset().top
    }, 'slow');
    return false;
});

// Send summary to side columns
function sendSummaryMenu()
{
    let left = jQuery('#menu-left');
    let right = jQuery('#menu-right');
    if (left.length != 0 && title.length != 0) {
        jQuery('#sheet-summary')
            .prependTo(left)
            .addClass('cell-mini');
    }
    else if (left.length == 0 && right.length != 0 && title.length != 0) {
        jQuery('#sheet-summary')
            .prependTo(right)
            .addClass('cell-mini');
    }
    else if (left.length == 0 && right.length == 0 && title.length != 0) {
        var newMenu = jQuery('<aside/>', { id: 'menu-left', class: 'aside-menu' }).prependTo('#global-container');
        jQuery('#main').addClass('main-with-left');
        jQuery('#sheet-summary')
            .appendTo(newMenu)
            .addClass('cell-mini')
            .css('position', 'sticky');
    }
}
    
if (window.matchMedia("(min-width: 769px)").matches) {
    sendSummaryMenu();
}

// Home page (root)
jQuery(document).ready(function () {
    function CreateChild(id) {
        var $li = jQuery('li[data_p_id="' + id + '"]').sort(function (a, b) {
            return jQuery(a).attr('data_order_id') - jQuery(b).attr('data_order_id');
        });
        if ($li.length > 0) {
            for (var i = 0; i < $li.length; i++) {
                var $this = $li.eq(i);
                $this.append(CreateChild($this.attr('data_id')));
            }
            return jQuery('<ul class="items-list-' + id + '">').append($li);
        }
    }

    jQuery('#category-nav li').has('ul').addClass('has-children');
    jQuery('#category-nav').find('.toggle-menu-button-0').removeClass('flex-between').css('display', 'none');
});

jQuery('[class*="toggle-menu-button"] .categories-item').each(function () {
    jQuery(this).on('click', function () {
        jQuery(this).toggleClass('is-open-menu');
        jQuery(this).closest('li').children('[class*="items-list"]').toggleClass('show-list');
    });
});

