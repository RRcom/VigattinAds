<?php
namespace VigattinAds\Controller\PageBlock;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class BlockNoGoldController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout('vigattinads/layout/emptyLayout');
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/pageblock/blockNoGold');
        return $viewModel;
    }
}
