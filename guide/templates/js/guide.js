/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
*/

// Summary menu constructor
var title = jQuery('.content .formatter-title:not(span)');
if (title.length == 0)
{
    jQuery('#sheet-summary').remove();
}
title.each(function () {
    var rewrited = jQuery(this).text().replace(/[^a-zA-Z0-9]/ig, "-").toLowerCase();
    jQuery(this).attr('id', rewrited);
    var innerhtml = jQuery(this).html();
    var padding = '';
    var hyphen = '<span>&vdash;</span>';
    if (jQuery(this).is('h2')) {padding = '0.618em'; hyphen = '<i class="fa fa-circle smaller"></i> '}
    if (jQuery(this).is('h3')) padding = '1.618em';
    if (jQuery(this).is('h4')) padding = '2.618em';
    if (jQuery(this).is('h5')) padding = '3.618em';
    if (jQuery(this).is('h6')) padding = '4.618em';
    jQuery('#summary-list').append(jQuery('<li><a class="summary-title" href="#' + rewrited + '" style="padding-left: ' + padding + '">' + hyphen + '<span class="inner-title">' + innerhtml + '</span></a></li>'));

    var anchor = jQuery('<a href="' + window.location.href + '#' + rewrited + '" class="smaller copy-anchor" aria-label="' + ARIA_TO_CLIPBOARD + '"><i class="fa fa-fw fa-hashtag" aria-hidden="true"></i></a>');
    jQuery(this).prepend(anchor);
});

// Copy anchor to clipboard and show when done
jQuery('.show-anchor').prependTo('body');

document.querySelectorAll('.copy-anchor').forEach( el => {
    el.addEventListener('click', event => {
        event.preventDefault();

        var hrefValue = el.getAttribute('href');

        document.addEventListener('copy', function(e) {
            e.clipboardData.setData('text/plain', hrefValue);
            e.preventDefault();
        }, true);

        document.execCommand('copy');
        show_anchor(COPIED_TO_CLIPBOARD + '<br /><br />' + hrefValue);
    });
});

function show_anchor(content) {
    jQuery('.show-anchor').addClass('active-anchor').children().html(content);
    setTimeout(() => {
        jQuery('.show-anchor').removeClass('active-anchor')
    }, 3000);
}

// smoth scroll
jQuery('.summary-title').on('click', function () {
    var idTarget = $(this).attr("href");

    $('html, body').animate({
        scrollTop: $(idTarget).offset().top
    }, 'slow');
    return false;
});

// Send summary menu to side columns
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
    jQuery('#category-nav').append(GuideCreateChild(0)).find('ul:first').remove();

    function GuideCreateChild(id) {
        var $li = jQuery('li[data_p_id="' + id + '"]').sort(function (a, b) {
            return jQuery(a).attr('data_order_id') - jQuery(b).attr('data_order_id');
        });
        if ($li.length > 0) {
            for (var i = 0; i < $li.length; i++) {
                var $this = $li.eq(i);
                $this.append(GuideCreateChild($this.attr('data_id')));
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

