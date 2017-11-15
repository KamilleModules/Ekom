<?php


namespace Module\Ekom\HybridList\HybridListControl\Slice;


use Bat\UriTool;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use Module\Ekom\HybridList\HybridListControl\HybridListControl;


/**
 *
 * The returned model is the page component of a listBundle model
 * https://github.com/lingtalfi/Models/tree/master/ListBundle
 *
 */
class PaginateSliceHybridListControl extends HybridListControl
{

    private $userPage; // user (tried) page
    private $nipp;
    private $linkCallback;


    public function __construct()
    {
        $this->userPage = 1;
        $this->nipp = 20;
    }


    /**
     * @param callable $linkCallback
     *              str:link   fn ( int:pageNumber, bool:isSelected )
     * @return $this
     */
    public function setLinkCallback(callable $linkCallback)
    {
        $this->linkCallback = $linkCallback;
        return $this;
    }


    public function setNumberOfItemsPerPage($nipp)
    {
        $this->nipp = $nipp;
        return $this;
    }


    public function prepareHybridList(HybridListInterface $list, array $context)
    {
        $pool = $context['pool'];

        $list->addListShaper(ListShaper::create()
            ->reactsTo(["*", "page"])
            ->setExecuteCallback(function ($input, array &$boxes, array &$info = [], array $originalBoxes) use ($pool) {

                if ('*' !== $input) {
                    $this->userPage = $input;
                } else {


                    $linkFn = $this->linkCallback;
                    if (null === $linkFn) {
                        $uriParams = $pool;
                        $uriParams['page'] = '%s';
                        $uri = UriTool::uri(null, $uriParams, true);
                        $linkFn = function ($i, $isSelected) use ($uri) {
                            return sprintf($uri, $i);

                        };
                    }
                    $nipp = $this->nipp; // is the user allowed to override it?

                    $nbItems = (int)$info['totalNumberOfItems'];
                    $maxPage = (int)ceil($nbItems / $nipp);
                    $page = (int)$this->userPage;
                    if ($page < 1) {
                        $page = 1;
                    } elseif ($page > $maxPage) {
                        $page = $maxPage;
                    }

                    $offset = ($page - 1) * $nipp;
                    $boxes = array_slice($boxes, $offset, $nipp);


                    //--------------------------------------------
                    // UPDATE THE INFO, since we update the structure...
                    //--------------------------------------------
                    $offsetEnd = $offset + $nipp;
                    if ($offsetEnd > $nbItems) {
                        $offsetEnd = $nbItems;
                    }
                    $info['sliceNumber'] = $page;
                    $info['sliceLength'] = $nipp;
                    $info['offset'] = $offset;
                    $info['offsetEnd'] = $offsetEnd;


                    //--------------------------------------------
                    // UPDATE THE MODEL
                    //--------------------------------------------
                    $items = [];
                    for ($i = 1; $i <= $maxPage; $i++) {
                        $selected = ($page === $i);
                        $items[] = [
                            "number" => $i,
                            "link" => call_user_func($linkFn, $i, $selected),
                            "selected" => $selected,
                        ];
                    }
                    $this->model = [
                        "currentPage" => $page,
                        "items" => $items,
                    ];
                }

            }));
        return $this;

    }

}