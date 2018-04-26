<?php


namespace Module\Ekom\View\Cart\Cartoon\ItemsDescription;


use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Helper\DiscountHelper;
use Module\Ekom\Utils\E;
use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;
use Module\ThisApp\Ekom\Helper\PriceHelper;

class ItemsDescriptionRenderer extends BaseRenderer
{

    private $items;

    protected $columnLabels;
    private $column2Property;

    public function __construct()
    {
        parent::__construct();
        $this->items = [];
        $this->columns = [
            'quantity',
            'image',
            'reference',
            'seller',
            'label',
            'labelAndDetails',
            'description',
            'originalPrice',
            'discount',
            'discountLabel',
            'basePrice',
            'tax',
            'taxLabel',
            'salePrice',
            'linePriceWithoutTax',
            'linePriceWithTax',
        ];

        $this->columnLabels = [
            'quantity' => "Quantité",
            'image' => "Visuel",
            'reference' => "Référence",
            'seller' => "Vendeur",
            'label' => "Libellé",
            'labelAndDetails' => "Libellé",
            'description' => "Description",
            'originalPrice' => "Prix original",
            'discount' => "Remise",
            'discountLabel' => "Libellé remise",
            'basePrice' => "Prix de base",
            'tax' => "Taxe",
            'taxLabel' => "Libellé taxe",
            'salePrice' => "Prix de vente",
            'linePriceWithoutTax' => "Prix ligne HT",
            'linePriceWithTax' => "Prix ligne TTC",
        ];

        $this->column2Property = [
            'quantity' => "cart_quantity",
            'image' => "image",
            'reference' => "reference",
            'seller' => "seller",
            'label' => "label",
            'labelAndDetails' => "label",
            'description' => "description",
            'originalPrice' => "original_price",
            'discount' => "discount_details",
            'discountLabel' => "discount_details",
            'basePrice' => "base_price",
            'tax' => "tax_details",
            'taxLabel' => "tax_details",
            'salePrice' => "sale_price",
            'linePriceWithoutTax' => "line_base_price",
            'linePriceWithTax' => "line_sale_price",
        ];

    }


    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function render()
    {
        ?>
        <table class="cartoon-items-description cartoon-columns">
            <tr>
                <?php foreach ($this->columns as $column): ?>
                    <td><?php echo $this->columnLabels[$column]; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($this->items as $item): ?>
                <tr>
                    <?php foreach ($this->columns as $column): ?>
                        <td><?php $this->renderColumnContent($column, $item); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getColumnContentImage(array $item)
    {
        ?>
        <img src="<?php echo htmlspecialchars($item['image']); ?>"
             alt="<?php echo htmlspecialchars($item['label']); ?>">
        <?php
    }

    protected function getColumnContentLabelAndDetails(array $item)
    {
        echo $item['label'];
        ?>
        <div class="product-properties">
            <?php if ($item['selected_attributes_info']): ?>
                <?php foreach ($item['selected_attributes_info'] as $attrItem): ?>
                    <div class="product-property">
                        <span class="name"><?php echo $attrItem['attribute_label']; ?>:</span>
                        <span class="value"><?php echo $attrItem['value_label']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($item['selected_product_details_info']): ?>
                <?php foreach ($item['selected_product_details_info'] as $detailItem): ?>
                    <div class="product-property">
                        <span class="name"><?php echo $detailItem['attribute_label']; ?>:</span>
                        <span class="value"><?php echo $detailItem['value_label']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }


    protected function getColumnContentDiscount(array $item)
    {
        if ($item['discount_details']) {
            echo E::price($item['line_base_price'] - $item['line_real_price']);
        }
    }

    protected function getColumnContentDiscountLabel(array $item)
    {
        if ($item['discount_details']) {
            /**
             * As for now, Ekom only handles one discount per item,
             * so we take the label of the first one only (in case multiple discounts were set)
             */
            echo $item['discount_details'][0]['label'];
        }
    }

    protected function getColumnContentTax(array $item)
    {
        if ($item['line_tax_details']) {
            $totalAmount = 0;
            foreach ($item['line_tax_details'] as $lineAmount) {
                $totalAmount += $lineAmount;
            }
            echo PriceHelper::priceAsDecimal($totalAmount);
        }
    }


    protected function getColumnContentTaxLabel(array $item)
    {
        if ($item['line_tax_details']) {
            echo implode(", ", array_keys($item['line_tax_details']));
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function renderColumnContent($column, array $item)
    {
        $overrideMethod = "getColumnContent" . ucfirst($column);
        if (method_exists($this, $overrideMethod)) {
            call_user_func([$this, $overrideMethod], $item);
        } else {
            $prop = $this->column2Property[$column];
            echo $item[$prop];
        }
    }
}