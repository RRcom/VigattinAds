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

    /**
     * @var string group name
     */
    protected $group;

    /**
     * @var bool
     */
    protected $disable = false;

    function __construct($keyword, $previewLink, $selected, $title, $group = '', $disable = false)
    {
        $this->keyword = $keyword;
        $this->previewLink = $previewLink;
        $this->selected = $selected;
        $this->title = $title;
        $this->group = $group;
        $this->disable = $disable;
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

    /**
     * @param string $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @param string $previewLink
     */
    public function setPreviewLink($previewLink)
    {
        $this->previewLink = $previewLink;
    }

    /**
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @param boolean $disable
     */
    public function setDisable($disable)
    {
        $this->disable = $disable;
    }

    /**
     * @return boolean
     */
    public function getDisable()
    {
        return $this->disable;
    }
}