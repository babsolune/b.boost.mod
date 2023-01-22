/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 21
 * @since       PHPBoost 5.1 - 2017 09 11
*/

jQuery(document).ready(function () {
    jQuery('#guide-mini-nav').append(DocMiniCreateChild(0)).find('ul:first').remove();

	function DocMiniCreateChild(id){
		var $li = jQuery('li[data-guide-parent-id="' + id + '"]').sort(function(a, b){
			return jQuery(a).attr('data-guide-order-id') - jQuery(b).attr('data-guide-order-id');
		});
		if($li.length > 0){
			for(var i = 0; i < $li.length; i++){
				var $this = $li.eq(i);
                $this.append(DocMiniCreateChild($this.attr('data-guide-id')));
			}
            return jQuery('<ul class="level-' + id + '">').append($li);
		}
    }

    jQuery('#guide-mini-nav .items-list li').each(function() {
        var target = jQuery(this).parent().siblings('[class^="level-"]');
        if(target)
            target.prepend(jQuery(this));
    });
});
