<?php


namespace Module\Ekom\View\Cart\Cartoon\Driver;


class Driver
{

    private $title;
    private $pool;
    private $possibleValue2Label;
    private $defaultValues;
    private $checkedItems;

    public function __construct()
    {
        $this->pool = $_POST;
        $this->possibleValue2Label = [];
        $this->defaultValues = [];
        $this->checkedItems = null;
    }

    public static function create()
    {
        return new static();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


    public function setPool(array $pool)
    {
        $this->pool = $pool;
        return $this;
    }

    public function setPossibleValue2Label(array $possibleValue2Label)
    {
        $this->possibleValue2Label = $possibleValue2Label;
        return $this;
    }

    public function setDefaultValues(array $defaultValues)
    {
        $this->defaultValues = $defaultValues;
        return $this;
    }


    public function render()
    {
        $this->prepare();
        $checkedItems = $this->checkedItems;
        ?>
        <div class="driver">
            <?php if ($this->title): ?>
                <h4><?php echo $this->title; ?></h4>
            <?php endif; ?>

            <?php foreach ($this->possibleValue2Label as $key => $value): ?>
                <div>
                    <input
                            id="id-<?php echo $key; ?>"
                            name="<?php echo $key; ?>"
                            value="1"
                        <?php if (array_key_exists($key, $checkedItems)): ?>
                            checked="checked"
                        <?php endif; ?>
                            type="checkbox">
                    <label for="id-<?php echo $key; ?>"><?php echo $value; ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public function getChecked()
    {
        $this->prepare();
        return array_keys($this->checkedItems);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function prepare()
    {
        if (null === $this->checkedItems) {
            $checkedItems = array_intersect_key($this->possibleValue2Label, $this->pool);
            if (empty($_POST)) {
                $checkedItems = $this->defaultValues;
                $checkedItems = array_flip($checkedItems);
            }
            $this->checkedItems = $checkedItems;
        }
    }
}