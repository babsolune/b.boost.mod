/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 02 19
 * @since       PHPBoost 5.1 - 2017 09 11
*/

(function ($)
{
    $.fn.menutree = function(options) {
        var defaults = jQuery(this), params = jQuery.extend({
            'oneSub': false,
            'openLink' : true
        }, options);

        return this.each(function() {
            // send .has-sub if li has ul
            jQuery(this)
                .find('li')
                .each(function() {
                    if (jQuery(this).find('ul').length > 0) {
                        jQuery(this).addClass('has-sub');
                    }
                });
            // on page loading close all ul
            jQuery(this).find('ul ul').each(function() { 
                jQuery(this).slideUp();
            });

            // openLink = Check on page loading if window location href is item url then show item
            if(params.openLink)
            {
                jQuery(this).find('a').each(function() {
                    var link = jQuery(this).attr('href');
                    if (window.location.href.indexOf(link) > -1) {
                        jQuery(this).addClass('active'); // add class to parent link (should be 'li')
                        if(jQuery(this).parents('.has-sub').length) 
                        {
                            // add .open to all parents li
                            jQuery(this)
                                .parents('.has-sub')
                                .addClass('open');  
                            // add .active to all parents item
                            jQuery(this)
                                .parents('.has-sub')
                                .find(' > a.menutree-title')
                                .addClass('active'); 
                            // open all parents ul
                            jQuery(this)
                                .parents('.has-sub > ul')
                                .slideDown(); 
                            // open ul siblings of item
                            jQuery(this)
                                .parents('ul')
                                .siblings('ul').
                                slideDown(); 
                            // Toggle parents and item folder icon
                            jQuery(this)
                                .parents('.open')
                                .find(' > .menutree-title .fa-folder')
                                .toggleClass('far fa-folder-open');
                            // If page is category
                            jQuery(this)
                                .parent('.open')
                                .removeClass('open')
                                .find(' > .menutree-title .fa-folder')
                                .toggleClass('far fa-folder-open'); 
                        }
                    }
                });
            }
            jQuery(this).find('ul > li > .swap-handle').on('click', function(event){
                event.stopPropagation();
                // toggle item
                jQuery(this).parent().toggleClass('open');
                if (jQuery(this).parent().hasClass('has-sub')) 
                {
                    jQuery(this)
                        .siblings('ul')
                        .slideToggle('fast');
                }
                // toggle folder icon
                jQuery(this)
                    .parent()
                    .find(' > .menutree-title .fa-folder')
                    .toggleClass('far fa-folder-open');
                // oneSub = close siblings when open item
                if (params.oneSub) 
                {
                    jQuery(this)
                        .parent()
                        .siblings()
                        .removeClass('open')
                        .find(' > .menutree-title .fa-folder')
                        .removeClass('far fa-folder-open');
                    jQuery(this)
                        .parent()
                        .siblings()
                        .find('ul')
                        .slideUp('fast');
                }
            })
        });
    };
})(jQuery);
