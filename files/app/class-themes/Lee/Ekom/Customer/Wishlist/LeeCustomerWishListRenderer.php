<?php


namespace Theme\Lee\Ekom\Customer\Wishlist;


use Module\ThisApp\Ekom\View\Hybrid\Info\InfoHybridWidget;
use Module\ThisApp\Ekom\View\Hybrid\Slice\PaginationHybridWidget;
use Module\ThisApp\Ekom\View\Hybrid\Sort\ProductSortHybridWidget;
use Theme\Lee\Ekom\Carousel\CarouselItemRenderer;
use Theme\Lee\Ekom\Carousel\WishListCarouselItemRenderer;

class LeeCustomerWishListRenderer
{


    public static function render(array $model)
    {
        $v = $model;
        ob_start();
        if (array_key_exists('listBundle', $v)) {
            $bundle = $v['listBundle'];
        } else {
            $bundle = $v['bundle'];
        }
        $items = $bundle['general']['items'];


        ?>


        <?php if ($items): ?>
        <?php if (array_key_exists('title', $v)): ?>
            <h4 class="widget-title"><?php echo $v['title']; ?></h4>
        <?php endif; ?>

        <?php if (false):
            // todo: some design if you want this feature
            ?>
            <?php if (array_key_exists("general", $bundle)): ?>
            <?php InfoHybridWidget::create()->render($bundle['general']); ?>
        <?php endif; ?>
        <?php endif; ?>


        <?php if (array_key_exists('sort', $bundle)): ?>
            <div class="sort-element">
                <?php ProductSortHybridWidget::create()->render($bundle); ?>
            </div>
        <?php endif; ?>


        <div class="wishlist-actions">
            <button class="lee-black-button remove-wishlist-btn bionic-btn"
                    data-action="user.removeWishlist"
                    data-directive-confirm_msg="Êtes-vous sûr(e) de vouloir supprimer toute votre liste de favoris?"
            >Supprimer ma liste
            </button>
        </div>

        <div class="cards-elements grid product-boxes">
            <?php
            $renderer = CarouselItemRenderer::create();
            foreach ($items as $p) {
                ?>
                <div class="another-container">
                    <?php
                    $renderer->render($p);
                    ?>
                    <a class="wishlist-delete-item bionic-btn"
                       data-action="user.removeWishlistItem"
                       data-param-product_id="<?php echo $p['product_id']; ?>"
                       href="#">Supprimer de ma liste</a>
                </div>
                <?php
            }
            ?>
        </div>
        <?php if (array_key_exists("slice", $bundle)): ?>
            <div class="pagination-element">
                <?php PaginationHybridWidget::create()->render($bundle['slice']); ?>
            </div>

        <?php endif; ?>

    <?php else: ?>
        <div class="central-statement">
            Aucun élément trouvé pour cette recherche.
        </div>
    <?php endif;
        return ob_get_clean();
    }

}
