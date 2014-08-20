<?php
namespace VigattinAds\DomainModel\AdsCategory;

class AdsCategory
{
    /**
     * @var string used to search this in database
     */
    protected $keyword;

    /**
     * @var string url to preview page
     */
    protected $previewLink;

    /**
     * @var bool true if user uses this category
     */
    protected $selected;

    /**
     * @var string title or label to display in view
     */
    protected $title;

    function __construct($keyword, $previewLink, $selected, $title)
    {
        $this->keyword = $keyword;
        $this->previewLink = $previewLink;
        $this->selected = $selected;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @return string
     */
    public function getPreviewLink()
    {
        return $this->previewLink;
    }

    /**
     * @return bool
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


}