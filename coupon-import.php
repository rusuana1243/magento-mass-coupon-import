<?php
use Magento\Framework\App\Bootstrap; 

/**
 * If your external file is in root folder
 */
require __DIR__ . '/app/bootstrap.php';

// Import CSV from ViArt format:
$handle = fopen('coupons.csv', 'r');
$cols   = array_flip(fgetcsv($handle));

while($data = fgetcsv($handle))
{

   
        echo 'Importing coupon with code: '.$data[$cols['coupon_code']].'<br />';
        createCoupon(
            $data[$cols['coupon_code']],
            $data[$cols['description']],
            'by_fixed',
            $data[$cols['discount_amount']]
        );
}


/**
* CREATE COUPONS
*/
function createCoupon($code, $description, $type, $amount, $options = array())
{

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
//$state->setAreaCode('frontend');
$state->setAreaCode('adminhtml');  

$coupon['name'] = 'Clearance Sale';
$coupon['desc'] = '';
$coupon['start'] = date('Y-m-d');
$coupon['end'] = '';
$coupon['max_redemptions'] = 1000;
$coupon['discount_type'] ='cart_fixed';
$coupon['discount_amount'] = 15;
$coupon['flag_is_free_shipping'] = 'no';
$coupon['redemptions'] = 1;
$coupon['code'] ='NL01-1234'; //this code will normally be autogenetated but i am hard coding for testing purposes

$shoppingCartPriceRule = $obj->create('Magento\SalesRule\Model\Rule');
$shoppingCartPriceRule->setName($coupon['name'])
        ->setDescription($description)
        ->setFromDate($coupon['start'])
        ->setToDate($coupon['end'])
        ->setUsesPerCustomer($coupon['max_redemptions'])
        ->setCustomerGroupIds(array('0','1','2','3',))
        ->setIsActive(1)
        ->setSimpleAction($coupon['discount_type'])
        ->setDiscountAmount($amount)
        ->setDiscountQty(1)
        ->setApplyToShipping($coupon['flag_is_free_shipping'])
        ->setTimesUsed($coupon['redemptions'])
        ->setWebsiteIds(array('1'))
        ->setCouponType(2)
        ->setCouponCode($code)
        ->setUsesPerCoupon(NULL);
$shoppingCartPriceRule->save();
}

?>
