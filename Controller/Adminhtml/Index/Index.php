<?php
declare(strict_types=1);

namespace Octocub\PurgeVarnishCache\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory 
     */
    protected $resultPageFactory;
    protected $helperData;
    public $_storeManager;
    protected $_messageManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Octocub\PurgeVarnishCache\Helper\Data $helperData,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->helperData = $helperData;
        $this->_storeManager=$storeManager;
        $this->_resultFactory = $context->getResultFactory();
        $this->_messageManager = $messageManager;
        parent::__construct($context, $messageManager, $helperData, $storeManager, $coreRegistry);
    }

    public function execute()
    {
        $portNumber = $this->helperData->getPortNumber();
        $BaseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $varnishUrl = rtrim($BaseUrl, '/').":".$portNumber;
        $curl = curl_init($varnishUrl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PURGE");
        $this->_messageManager->addSuccessMessage('Varnish cache has been successfully purged. Enjoy the optimized performance and refreshed content on your website');
        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return true;
    }
}