
// @copyright   &copy; 2005-2023 PHPBoost
// @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
// @author      Sebastien LARTIGUE <babsolune@phpboost.com>
// @version     PHPBoost 6.0 - last update: 2023 10 07
// @since       PHPBoost 6.0 - 2023 10 07


jQuery(document).ready(function(){
	jQuery('#category-nav').append(CreateChild(0)).find('ul:first').remove();

	function CreateChild(id){
		var $li = jQuery('li[data-p-id="' + id + '"]').sort(function(a, b){
			return jQuery(a).data('order-id') - jQuery(b).data('order-id');
		});
		if($li.length > 0){
			for(var i = 0; i < $li.length; i++){
				var $this = $li.eq(i);
				$this.append(CreateChild($this.data('id')));
			}
			return jQuery('<ul class="items-list-'+id+'">').append($li);
		}
	}

	jQuery('#category-nav li').has('ul').addClass('has-children');
});

    console.log(jQuery('[class*="toggle-menu-button"] > .categories-item'));
jQuery('[class*="toggle-menu-button"] .categories-item').each(function() {
	jQuery(this).on('click', function(){
		jQuery(this).toggleClass('is-open-menu');
		jQuery(this).closest('li').children('[class*="items-list"]').toggleClass('show-list');
	});
});
