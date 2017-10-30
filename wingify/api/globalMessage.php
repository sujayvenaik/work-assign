<?php 
//define('CHECKOUT_EXCEPTION','User authentication failed please try to login again..');
define('CHECKOUT_EXCEPTION','There was some error processing your order. Please go back and checkout once again.');
define('CHECKOUT_ADDRESS_MISMATCH','Address not match.');
define('TIMESLOT_EXPIRE','Your chosen time slot has expired, please select a different time slot for delivery.');

//shipping messages
define('EXTENDED_SHIPPING_MSG','A delivery charge of ₹{extendedPrice} is added in your cart amount as your selected area is an extended serviceable area.');
define('SHIPPING_MSG','A delivery charge of ₹{flateratePrice} is added in your cart amount. *Delivery charges are applied for cart value less than ₹{minimum} ( exclusive of taxes ) .');
define('SHIPPING_CAKE_MSG','A delivery charge of ₹{flateratePrice} will be applicable.');

//on demand messages

//On-Demand
define('ONDEMAND_NONBULK','Your order has been placed and will be delivered by {time}.');
define('ONDEMAND_BULK',"Your order has been placed and we'll strive to deliver it by {time} (may take 25 mins more due to bulk quantity).");
define('ONDEMAND_EXTENDED_AREA_MSG',"Your order has been placed and we'll strive to deliver it by {time} (may take 25 mins more due to address falling under extended service area).");

//Scheduled later
define('SCHEDULEDLATER_NONBULK','Your order has been placed and will be delivered by {time} on {date}.');
define('SCHEDULEDLATER_BULK',"Your order has been placed and will be delivered by {time} on {date}.");
define('AREA_NOT_SERVICEABLE','Sorry. We cannot service your selected delivery area this time. Inconvenience caused is regretted.');
define('TIME_SLOT_MISSING','Please select time slot and order again.');
?>