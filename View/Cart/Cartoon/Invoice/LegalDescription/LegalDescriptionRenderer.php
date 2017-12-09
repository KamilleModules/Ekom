<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\LegalDescription;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class LegalDescriptionRenderer extends BaseRenderer
{

    private $text;

    public function __construct()
    {
        parent::__construct();
        $this->text = "Cette attestation est la preuve que vous avez bien réalisé l'achat du bon de commande
        décrit sur cette page.";
        $this->columns = [
            'text',
        ];
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }


    public function render()
    {

        if (true === $this->has("text")): ?>
            <div class="legal-description"><?php echo $this->text; ?></div>
        <?php
        endif;
    }
}