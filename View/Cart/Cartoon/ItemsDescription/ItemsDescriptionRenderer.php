<?php


namespace Module\Ekom\View\Cart\Cartoon\ItemsDescription;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

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
            'quantity' => "quantityCart",
            'image' => "imageThumb",
            'reference' => "ref",
            'seller' => "seller",
            'label' => "label",
            'labelAndDetails' => "label",
            'description' => "description",
            'originalPrice' => "priceOriginalRaw",
            'discount' => "discountRawSavingFixed",
            'discountLabel' => "discountLabel",
            'basePrice' => "priceBaseRaw",
            'tax' => "taxAmount",
            'taxLabel' => "taxGroupLabel",
            'salePrice' => "priceSaleRaw",
            'linePriceWithoutTax' => "priceLineWithoutTaxRaw",
            'linePriceWithTax' => "priceLineRaw",
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
        <img src="<?php echo htmlspecialchars($item['imageThumb']); ?>"
             alt="<?php echo htmlspecialchars($item['label']); ?>">
        <?php
    }

    protected function getColumnContentLabelAndDetails(array $item)
    {
        echo $item['label'];
        ?>
        <div class="product-properties">
            <?php if ($item['attributesSelection']): ?>
                <?php foreach ($item['attributesSelection'] as $attrItem): ?>
                    <div class="product-property">
                        <span class="name"><?php echo $attrItem['attribute_label']; ?>:</span>
                        <span class="value"><?php echo $attrItem['value_label']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($item['productDetailsSelection']): ?>
                <?php foreach ($item['productDetailsSelection'] as $detailItem): ?>
                    <div class="product-property">
                        <span class="name"><?php echo $detailItem['attribute_label']; ?>:</span>
                        <span class="value"><?php echo $detailItem['value_label']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
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