<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\Footer;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class FooterRenderer extends BaseRenderer
{

    private $text;

    public function __construct()
    {
        parent::__construct();
        $this->text = "";
        $this->columns = [
            'footer_text',
        ];
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }


    public function render()
    {

        if (true === $this->has("footer_text")): ?>
            <div class="footer">
                <hr>
                <?php echo $this->text; ?>
            </div>
        <?php
        endif;
    }
}