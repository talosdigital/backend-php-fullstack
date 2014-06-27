<?php

namespace Sales\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use User\Helper\UserHelper;
use User\Service\UserService;

use Sales\Service\QuoteService;
use Sales\Service\OrderService;
use Sales\Adapter\PayPal\PayPalPaymentsPro;
use Sales\Helper\OrderHelper;
use Sales\Helper\QuoteHelper;
use Sales\Document\Item;

/**
 *
 * @SWG\Model(id="checkout")
 */
class CheckoutController extends AbstractRestfulController
{
    
    /** @SWG\Resource(
    *   resourcePath="sales",
    *   basePath = "api/sales/checkout")
    */

    /**
     *
     * @SWG\Api(
     *   path="/payment",
     *    @SWG\Operation(
     *      nickname="payment",
     *      method = "POST",
     *      summary="payment usign paypal API",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="billingAddress",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "0"
     *          ),
     *          @SWG\Parameter(
     *              name="creditCardNumber",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "5363853692165683"
     *          ),
     *          @SWG\Parameter(
     *              name="creditcardType",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "visa"
     *          ),
     *          @SWG\Parameter(
     *              name="cvv2",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "311"
     *          ),
     *          @SWG\Parameter(
     *              name="cardholderName",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "Javier Franco"
     *          ),
     *          @SWG\Parameter(
     *              name="expiryDateDay",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "01"
     *          ),
     *          @SWG\Parameter(
     *              name="expiryDateYear",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "2015"
     *          )
     *      )
     *   )
     *  )
     */

    public function paymentAction() 
    {   
        // Start all the services and helpers to use
        $this->quoteService = new QuoteService($this->getServiceLocator());
        $this->orderHelper = new OrderHelper($this->getServiceLocator());
        $this->orderService = new OrderService($this->getServiceLocator());

        $request = $this->getRequest()->getPost();
        
        $billingAddress = $request->get('billingAddress');

        // Save billing details to the quote a form data
        $user = $this->zfcUserAuthentication()->getIdentity();
        $user = UserHelper::getCurrentUser($user);

        $quote = new QuoteHelper($this->getServiceLocator());

        $quote = $quote->getQuote($user);
        
        // Update billing details
        $address = $user->getAddresses()->get($billingAddress);        
        $quote->setBillingDetails($address);

        // Get config
        $config = $this->getServiceLocator()->get("Config");

        $payPalItem = $config["sales"]["default_product"];

        // Create a fake item to buy
        $item = new Item();
        $item->setName($payPalItem["name"]);
        $item->setPrice($payPalItem["price"]);
        $item->setQuantity($payPalItem["quantity"]);

        $quote->getItems()->add($item);

        $quote->setTotals(array('total' => $payPalItem["price"]*$payPalItem["quantity"]));
        
        // map billing e-mail
        $quote->setEmail($user->getEmail());
        $this->quoteService->save($quote);

        $config = $config["sales"]["payment_adapters"]["paypal"];
        
        /* PAYMENTS PRO  GATEWAY*/
        $gateway = new PayPalPaymentsPro($config);
        
        $creditCardDetails = array(
           'credit_card_number' => $request->get("creditCardNumber"),
           'creditcard_type' =>    $request->get("creditcardType"),
           'cvv2' =>               $request->get("cvv2"),
           'cardholder_name' =>    $request->get("cardholderName"),
           'expiry_date_day' =>    $request->get("expiryDateDay"),
           'expiry_date_year' =>   $request->get("expiryDateYear")
        );

        $billingDetails = $user->getAddresses()->get($billingAddress);
        $result = $gateway->sendRequest($quote, "purchase", $creditCardDetails);

        if ($result == PayPalPaymentsPro::RESPONSE_OK) {

            $order = $this->orderHelper->createOrderFromQuote($quote);
            $transactionDetails = $gateway->getTransactionDetails();
            $order->setTransactionDetails($transactionDetails);
            $order = $this->orderService->save($order);
            
            return new JsonModel(array('message' => 'success'));

        } 
        else {
            throw new \Exception($result, \Sales\Module::ERROR_PAYPAL_ERROR);
        }
        
    }
        
}