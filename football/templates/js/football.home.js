
// @copyright   &copy; 2005-2023 PHPBoost
// @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
// @author      Sebastien LARTIGUE <babsolune@phpboost.com>
// @version     PHPBoost 6.0 - last update: 2023 10 07
// @since       PHPBoost 6.0 - 2023 10 07

document.addEventListener('DOMContentLoaded', () => {
    const categoryNav = document.getElementById('category-nav');
    categoryNav.appendChild(createChild(0));
    const firstUl = categoryNav.querySelector('ul:first-of-type');
    if (firstUl) {
        firstUl.remove();
    }

    function createChild(id) {
        const liElements = Array.from(document.querySelectorAll(`li[data-p-id="${id}"]`)).sort((a, b) => {
            return a.getAttribute('data-order-id') - b.getAttribute('data-order-id');
        });

        if (liElements.length > 0) {
            for (const li of liElements) {
                const childUl = createChild(li.getAttribute('data-id'));
                if (childUl) {
                    li.appendChild(childUl);
                }
            }

            const ul = document.createElement('ul');
            ul.className = `items-list-${id}`;
            liElements.forEach(li => {
                ul.appendChild(li);
            });
            return ul;
        } else {
            return null;
        }
    }

    const categoryNavLiElements = document.querySelectorAll('#category-nav li');
    categoryNavLiElements.forEach(li => {
        if (li.querySelector('ul')) {
            li.classList.add('has-children');
        }
    });

    const toggleMenuButtons = document.querySelectorAll('[class*="toggle-menu-button"] .categories-item');
    toggleMenuButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.classList.toggle('is-open-menu');
            const closestLi = button.closest('li');
            const itemsList = closestLi.querySelector('[class*="items-list"]');
            if (itemsList) {
                itemsList.classList.toggle('show-list');
            }
        });
    });
});