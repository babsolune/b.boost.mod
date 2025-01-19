
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/
jQuery(document).ready(function(){
	jQuery('#category-nav').append(CreateChild(0)).find('ul:first').remove();

	function CreateChild(id){
		var $li = jQuery('li[data-p-id="' + id + '"]').sort(function(a, b){
			return jQuery(a).attr('data-order-id') - jQuery(b).attr('data-order-id');
		});
		if($li.length > 0){
			for(var i = 0; i < $li.length; i++){
				var $this = $li.eq(i);
				$this.append(CreateChild($this.attr('data-id')));
			}
			return jQuery('<ul class="items-list-'+id+'">').append($li);
		}
	}

	jQuery('#category-nav li').has('ul').addClass('has-children');

    jQuery('[class*="toggle-menu-button"] .categories-item').each(function(){
        jQuery(this).on('click', function(){
            jQuery(this).toggleClass('is-open-menu');
            jQuery(this).closest('li').children('[class*="items-list"]').toggleClass('show-list');
        });
    });

    // Auto open li if filled
    const li = jQuery('.has-children');
    li.each(function(item) {
        const child = jQuery(this).children('.categories-item');
        const ul = jQuery(this).children('[class*="items-list-"]');
        if (child && ul)
        {
            child.addClass('is-open-menu');
            ul.addClass('show-list');
        }
    })
});