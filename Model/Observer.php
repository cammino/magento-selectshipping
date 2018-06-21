<?php
class Cammino_Selectshipping_Model_Observer extends Varien_Object {

    public function selectShipping(Varien_Event_Observer $observer)
    {
        $controller = $observer->getControllerAction();

        if($controller->getFullActionName() == 'checkout_cart_estimatePost') {

            $this->getSession()->setEstimatedCart('estimated');

        } else if ($controller->getFullActionName() == 'checkout_cart_index') {

            if ($this->getSession()->getEstimatedCart()) {

                $currentShippingMethod = $this->getQuote()->getShippingAddress()->getShippingMethod();
                $rates = $this->getQuote()->getShippingAddress()->getAllShippingRates();
                $firstRateCode = null;

                foreach ($rates as $rate) {
                    $firstRateCode = $rate->getCode();
                    break;
                }

                if (($currentShippingMethod == null) && ($firstRateCode != null)) {
                    $this->getQuote()->getShippingAddress()->setShippingMethod($firstRateCode)->save();
                }

                $this->getSession()->unsEstimatedCart();
                
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"));
            }
        }
    }

    private function getQuote() {
        $cart = Mage::getSingleton('checkout/cart');
        return $cart->getQuote();
    }

    private function getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}