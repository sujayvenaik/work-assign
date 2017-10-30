<?php

include_once 'constant.php';
include_once '../classes/aesCall.php';
// include '../api_config.php';
require_once ('../../app/Mage.php');
$fp = fopen('php://input', 'r');
$rawData = stream_get_contents($fp);
ini_set('default_socket_timeout', 600);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Mage::init();
Mage::app();
$getHeader = emu_getallheaders();
$authToken = $getHeader['Authtoken'];
$imei = $getHeader['Imei'];
if ($authToken == "fjhgkjsgqjkdgfjhwelfwlefhwelfwl" || $authToken == "DbQkDO1fm8U9VzcQn6v86vKp9kFbneO9") {
    
} else {
    echo $msg = "User authentication failed. Please login again.";
    return $msg;
}
$message = "User authentication failed. Please login again.";
//Mage::log("IMEI -  " . $imei . " Auth Token - " . $authToken, null, 'mobile_api.log');
//Mage::log("Request Raw (Post) -  " . $rawData, null, 'mobile_api.log');
if (count(json_decode($rawData)) != 0) {
    $postedJson = json_decode($rawData);
    //Mage::log ( "Request Raw -  " . print_r ( $postedJson, true ), null, 'mobile_api.log' );

    if ($postedJson->type == "dotin_authenticate") {
        if ($postedJson->opcode == "login") {
            require_once LOGIN;
            $emailId = $postedJson->emailId;
            $password = $postedJson->password;
            $storeId = $postedJson->storeId;
            $customer = new customer_login ();

            if (array_key_exists("source", $postedJson)) {
                $source = $postedJson->source;
                $token = $postedJson->token;
                $sourceId = $postedJson->sourceId;
                $response = $customer->innerchefLogin($emailId, $password, '1', $source, $sourceId, $token, $imei);
            } else {
                //echo "in else";
                $response = $customer->login($emailId, $password, '1');
            }
        } elseif ($postedJson->opcode == "logout") {
            require_once LOGIN;
            $customer = new customer_login ();
            $emailId = $postedJson->emailId;
            $password = $postedJson->password;
            $storeId = $postedJson->storeId;
            $response = $customer->logout_user($emailId, $password, '1');
        }
    } else if ($postedJson->type == "category") {
        require_once CATEGORY;
        $category = new category ();
        $storeId = $postedJson->storeId;
        // $storeName=$category->storeName($storeId);
        $response = $category->categoryTree($storeId);
    } else if ($postedJson->type == "aes") {
        //require_once AES;
        $aes = new aesc();
        $token = $postedJson->token;
        $imei = $postedJson->imei;
        $response = $aes->aescall($token, $imei);
    } else if ($postedJson->type == "getProductOutofStock") {
        require_once CATEGORY;
        $category = new category ();
        $prodct = $postedJson->prodct;
        // $storeName=$category->storeName($storeId);
        $response = $category->getproduct($prodct);
    } else if ($postedJson->type == "baker") {
        require_once BAKER;
        $baker = new baker ();
        $name = $postedJson->name;
        $email = $postedJson->email;
        $telephone = $postedJson->telephone;
        $address = $postedJson->address;
        $city = $postedJson->city;
        $comment = $postedJson->comment;
        $response = $baker->createBaker($name, $email, $telephone, $address, $city, $comment);
    }

    //new api get time slot storewise
    if ($postedJson->type == "timeSlot") {
        require_once TIMESLOT;
        $storeId = $postedJson->storeId;
        $cart = new cart ();
        $response = $cart->time_slots($storeId);
    } else if ($postedJson->type == "dotin_signup") {
        require_once CUSSIGNUP;
        $customer = new customer ();
        $emailId = $postedJson->emailId;
        $password = $postedJson->password;
        $storeId = $postedJson->storeId;
        $platform = $postedJson->platform;
        $firstname = $postedJson->firstname;
        $lastname = $postedJson->lastname;
        $source = $postedJson->source;
        $token = $postedJson->token;
        $sourceId = $postedJson->sourceId;
        $mobile = $postedJson->mobile;
        $isVerified = $postedJson->isVerified;
        $referralCode = $postedJson->referralCode;
        $response = $customer->innerchefCustomerSignup($emailId, $password, $firstname, $lastname, $storeId, $token, $source, $sourceId, $platform, $mobile, $isVerified, $referralCode, $imei);
    } else if ($postedJson->type == "popup") {
        date_default_timezone_set('Asia/Kolkata');
        require_once CAROUSEL;
        $crasual = new crasual ();
        $token = $postedJson->customerId;
        $storeId = $postedJson->storeId;
        $customerId = checkAesCustomer($token, $imei);
        $response = $crasual->apppopup($storeId, $customerId, $imei);
    } else if ($postedJson->type == "search") {
        require_once SEARCH;
        $search = new search ();
        $text = $postedJson->text;
        $storeId = $postedJson->storeId;
        // $storeName=$category->storeName($storeId);
        $response = $search->productSearch($text, $storeId);
    } else if ($postedJson->type == "product_list") {
        require_once PRODUCTLIST;
        $catId = $postedJson->catId;
        $productlist = new product ();
        $response = $productlist->productList($catId);
    } else if ($postedJson->type == "updatePassword") {
        require_once CUSTPROFILE;
        //$customerId = $postedJson->customerId;
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            $email = $postedJson->emailId;
            $oldPassword = $postedJson->oldPassword;
            $newPassword = $postedJson->newPassword;
            $imei = $postedJson->imei;
            $token = $postedJson->token;
            /* $aes = new aesc();
              $response = $aes->aescall ($customerId,$imei,$token); */
            $customer = new customer ();
            $response = $customer->updatePassword($customerId, $email, $oldPassword, $newPassword);
            $arr = array(
                "status" => "1",
                "data" => json_decode($response),
                "message" => ""
            );
            $response = json_encode($arr);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "forgotPassword") {
        require_once CUSTPROFILE;
        $email = $postedJson->emailId;
        $customer = new customer ();
        $response = $customer->forgotPassword($email);
    } else if ($postedJson->type == "updateProfile") {
        require_once CUSTPROFILE;
        //$customerId = $postedJson->customerId;
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            $firstname = $postedJson->firstname;
            $lastname = $postedJson->lastname;
            $email = $postedJson->emailId;
            // $profile_image = $postedJson->profile_image;
            // $default_billing = $postedJson->default_billing;
            // $default_shipping = $postedJson->default_shipping;
            $password = $postedJson->password;
            $customer = new customer ();
            $response = $customer->updateProfile($customerId, $email, $firstname, $lastname, $password);
            $arr = array(
                "status" => "1",
                "data" => json_decode($response),
                "message" => ""
            );
            $response = json_encode($arr);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "dotin_customerAddress") {
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        //Mage::log("Address Customer ID:: " . $customerId . " Token :: " . $token . " IMEI :: " . $imei, null, 'address.log');
        if (!empty($customerId)) {
            if ($postedJson->opcode == "createAddress") {
                require_once CUSTADDRESS;
                $firstname = $postedJson->firstname;
                $lastname = $postedJson->lastname;
                $street = $postedJson->street;
                $area = $postedJson->area;
                $city = $postedJson->store;
                $region = $postedJson->region;
                $region_id = $postedJson->region_id;
                $postcode = $postedJson->postcode;
                $telephone = $postedJson->telephone;
                $addressType = $postedJson->addressType;
                $addressCompany = $postedJson->addressCompany;
                $addressFlatno = $postedJson->addressFlatno;
                $default_billing = $postedJson->default_billing;
                $default_shipping = $postedJson->default_shipping;

                $address = new address ();
                $response = $address->createAddress($customerId, $firstname, $lastname, $street, $area, $city, $region, $region_id, $postcode, $telephone, $addressType, $addressCompany, $addressFlatno, $default_billing, $default_shipping);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            } else if ($postedJson->opcode == "updateAddress") {
                require_once CUSTADDRESS;
                $addressId = $postedJson->addressId;
                $firstname = $postedJson->firstname;
                $lastname = $postedJson->lastname;
                $street = $postedJson->street;
                $area = $postedJson->area;
                $city = $postedJson->store;
                $region = $postedJson->region;
                $region_id = $postedJson->region_id;
                $postcode = $postedJson->postcode;
                $telephone = $postedJson->telephone;
                $addressType = $postedJson->addressType;
                $addressCompany = $postedJson->addressCompany;
                $addressFlatno = $postedJson->addressFlatno;
                $default_billing = $postedJson->default_billing;
                $default_shipping = $postedJson->default_shipping;

                $address = new address ();
                $response = $address->updateAddress($addressId, $firstname, $lastname, $street, $area, $city, $region, $region_id, $postcode, $telephone, $addressType, $addressCompany, $addressFlatno, $default_billing, $default_shipping, $customerId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            } else if ($postedJson->opcode == "deleteAddress") {
                require_once CUSTADDRESS;
                $addressId = $postedJson->addressId;

                $address = new address ();
                $response = $address->deleteAddress($addressId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            } else if ($postedJson->opcode == "listAddress") {
                require_once CUSTADDRESS;
                //$customerId = $postedJson->customerId;

                $address = new address ();
                $response = $address->listAddress($customerId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            }
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "menu") {
        $token = $postedJson->customerId;
        if (!empty($token)) {
            $customerId = checkAesCustomer($token, $imei);
        } else {
            $customerId = "NULL";
        }
        if (!empty($customerId)) {
            require_once MENU;
            $customerId = $postedJson->customerId;
            $menu = new menu ();
            $response = $menu->getMenu($customerId);
            $arr = array(
                "status" => "1",
                "data" => json_decode($response),
                "message" => ""
            );
            $response = json_encode($arr);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "productinfo") {
        require_once PRODUCTINFO;
        $productId = $postedJson->productId;
        $product = new product ();
        $response = $product->productInfo($productId);
    } else if ($postedJson->type == "productinfoDeeplink") {
        require_once PRODUCTINFONEW;
        $productId = $postedJson->productId;
        $product = new productdepplink ();
        $response = $product->productInfoDeeplink($productId);
    } else if ($postedJson->type == "bulkorder") {
        require_once BULKORDER;
        //$productId = $postedJson->productId;
        $bulkOrder = new bulkOrder ();
        $response = $bulkOrder->bulkOrderRequest();
    } else if ($postedJson->type == "order_history") {
        require_once ORDERHISTORY;
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            // $customerId = $postedJson->customerId;
            $order = new order ();
            $response = $order->customerOrder($customerId);
            $arr = array(
                "status" => "1",
                "data" => json_decode($response),
                "message" => ""
            );
            $response = json_encode($arr);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "order_status") {
        require_once ORDERHISTORY;
        $orderIncrementId = $postedJson->orderId;
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            $order = new order ();
            $response = $order->orderStatus($orderIncrementId);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "order_info") {
        require_once ORDERHISTORY;
        $orderIncrementId = $postedJson->orderIncrementId;
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            // $customerId = $postedJson->customerId;
            $order = new order ();
            $response = $order->customerInfo($orderIncrementId);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "contact_us") {
        require_once CONTACT;
        $name = $postedJson->name;
        $email = $postedJson->email;
        $mob = $postedJson->mob;
        $comment = $postedJson->comment;
        $contact = new contact ();
        $response = $contact->contactUs($name, $email, $mob, $comment);
    } else if ($postedJson->type == "getCrossSellProduct") {
        require_once CROSSSELPRO;
        $product = $postedJson->product;
        $storeId = $postedJson->storeId;
        $crossSell = new crossSell ();
        $response = $crossSell->getCrossSellProduct($product, $storeId);
    } else if ($postedJson->type == "referral") {
        require_once REFERRAL;
        //$customerId = $postedJson->customerId;
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            $referral = new referral();
            $res = $referral->referralData($customerId);
            $arr = array(
                "status" => "1",
                "data" => json_decode($res),
                "message" => ""
            );
            $response = json_encode($arr);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "otp") {
        require_once OTP;
        $mobile = $postedJson->mobile;
        $mobileOTP = $postedJson->otp;
        $refCode = $postedJson->referralCode;
        $otp = new otp();
        $response = $otp->sendOTP($mobile, $mobileOTP, $refCode);
    } else if ($postedJson->type == "orderTIME") {
        require_once __DIR__ . '/../classes/order_time.php';
        $quote = Mage::getModel('sales/quote')->load($postedJson->quote);
        $orderType = $postedJson->orderType;
        $storeId = $postedJson->storeId;
        $orderTIME = new orderTIME();
        $response = $orderTIME->calculateTime($quote, $orderType, $storeId);
    } else if ($postedJson->type == "dotin_checkout") {
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            if ($postedJson->opcode == "create") {
                require_once CHECKOUTNEW;
                $shoppingCartId = $postedJson->shoppingCartId;
                $product = $postedJson->product;
                //$customerId = $postedJson->customerId;
                $couponCode = $postedJson->couponCode;
                $storeId = $postedJson->storeId;
                $addressId = $postedJson->addressId;
                $deliveryDate = $postedJson->deliveryDate;
                $timeSlot = $postedJson->timeSlot;
                $orderType = $postedJson->orderType;
                //$paymentMethod = $postedJson->paymentMethod;
                $checkout = new checkout ();
                $response = $checkout->proceedCheckout($shoppingCartId, $product, $customerId, $couponCode, $addressId, $deliveryDate, $timeSlot, $storeId, $orderType);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            }
            /* elseif ($postedJson->opcode == "wallet") {
              require_once CHECKOUTNEW;
              $shoppingCartId = $postedJson->shoppingCartId;
              $amount = $postedJson->amount;
              $checkout = new checkout();
              $response = $checkout->useWallet($shoppingCartId, $amount);
              } */ elseif ($postedJson->opcode == "addCoupon") {
                require_once CHECKOUTNEW;
                $shoppingCartId = $postedJson->shoppingCartId;
                $couponCode = $postedJson->couponCode;
                $checkout = new checkout();
                $response = $checkout->addCoupon($shoppingCartId, $couponCode);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            } elseif ($postedJson->opcode == "cancelCoupon") {
                require_once CHECKOUTNEW;
                $shoppingCartId = $postedJson->shoppingCartId;
                $checkout = new checkout();
                $response = $checkout->cancelCoupon($shoppingCartId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            } else if ($postedJson->opcode == "paymentMethod") {
                require_once CHECKOUTNEW;
                $shoppingCartId = $postedJson->shoppingCartId;
                $paymentMethod = $postedJson->paymentMethod;
                $addressId = $postedJson->addressId;
                Mage::log("now-" . date("Y-m-d H:i:s") ."-shoppingCartId-" . $shoppingCartId . " && method - " . $paymentMethod . " addressid- " . $addressId , null, 'payment_method.log');
                $checkout = new checkout ();
                $response = $checkout->paymentMethod($shoppingCartId, $paymentMethod, $addressId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
                Mage::log("now-" . date("Y-m-d H:i:s") ."-response-" . $response , null, 'payment_method.log');
            } else if ($postedJson->opcode == "cancelOrder") {
                require_once CHECKOUTNEW;
                $orderId = $postedJson->orderId;
                $checkout = new checkout ();
                $response = $checkout->cancelOrder($orderId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            } else if ($postedJson->opcode == "createOrder") {
                require_once CHECKOUTNEW;
                $shoppingCartId = $postedJson->shoppingCartId;
                $comment = $postedJson->comment;
                $source = $postedJson->source;
                $referralCode = $postedJson->referralCode;
                $amount = $postedJson->amount;
                $orderType = $postedJson->orderType;
                Mage::log("now-" . date("Y-m-d H:i:s") ."shoppingCartId-" . $shoppingCartId . " && source - " . $source . " referralcode- " . $referralCode . " -amount- " . $amount . " -order type- " . $orderType , null, 'create_order.log');
                $checkout = new checkout ();
                $response = $checkout->createOrder($shoppingCartId, $comment, $source, $referralCode, $amount, $orderType);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
                Mage::log("now-" . date("Y-m-d H:i:s") ."-response-" . $response , null, 'create_order.log');
            }
        } else {
            $arr = array(
                "status" => "0",
                "message" => $message
            );
            $response = json_encode($arr);
        }
    } else if ($postedJson->type == "dotin_payment") {
        $token = $postedJson->customerId;
        $customerId = checkAesCustomer($token, $imei);
        if (!empty($customerId)) {
            if ($postedJson->opcode == "paymentResponse") {
                require_once CHECKOUTNEW;
                $provider = $postedJson->provider;
                $orderId = $postedJson->orderId;
                $paymentId = $postedJson->paymentId;
                $status = $postedJson->status;
                $paymentMethod = $postedJson->paymentMethod;
                $addressId = $postedJson->addressId;
                $checkout = new checkout ();
                $response = $checkout->paymentResponse($provider, $orderId, $paymentId, $status, $paymentMethod, $addressId, $customerId);
                $arr = array(
                    "status" => "1",
                    "data" => json_decode($response),
                    "message" => ""
                );
                $response = json_encode($arr);
            }
        }
    } else if ($postedJson->type == "pushToken") {
        require_once PUSH;
        $token = $postedJson->token;
        $deviceType = $postedJson->deviceType;
        $imei = $postedJson->imei;
        $push = new push ();
        $response = $push->pushNotification($token, $deviceType, $imei);


        /* $token = $postedJson->token;
          $deviceType = $postedJson->deviceType;
          $imei = $postedJson->imei;
          $msg='Token: '.$token.', Type: '. $type.', IMEI: '.$imei. 'Date'. now();
          Mage::log ( "Tokan Register -  " . $msg, null, 'push_notification.log');
          if($token!="" && $deviceType!="" && $imei!="")
          {
          $arr=array(
          "status"=>"1",
          "message"=>'Save Successfully',
          );
          }
          else
          {
          $arr=array(
          "status"=>"0",
          "message"=>'Some Error Occurred',
          );
          }
          $response=json_encode($arr); */
    }

    /* 	else if ($postedJson->type == "order_status") {
      require_once ORDERSTATUS;
      $orderIncrementId = $postedJson->orderIncrementId;
      // $customerId = $postedJson->customerId;
      $order = new order_status ();
      $response = $order->orderStatus( $orderIncrementId );
      } */ else if ($postedJson->type == "coupon") {
        if ($postedJson->opcode == "apply") {
            require_once COUPON;
            $code = $postedJson->code;
            $coupon = new coupon ();
            $response = $coupon->applyCoupon($code);
        } else if ($postedJson->opcode == "cancel") {
            require_once COUPON;
            $code = $postedJson->code;
            $coupon = new coupon ();
            $response = $coupon->cancelCoupon($code);
        }
    } else if ($postedJson->type == "location") {
        require_once STORE;
        $store = new store ();
        $lat = $postedJson->lat;
        $long = $postedJson->long;
        $response = $store->storeLocation($lat,$long);
    } else if ($postedJson->type == "arealocation") {
        require_once STORE;
        $store = new store ();
        $area = $postedJson->area;
        $response = $store->storeLocationByArea($area);
    } else if ($postedJson->type == "landing_screen") {
        date_default_timezone_set('Asia/Kolkata');
        require_once CATEGORYLIST;
        $category = new categoryList ();
        $storeId = $postedJson->storeId;
        $token = $postedJson->customerId;
        $customerId = '';
        if(!empty($token)) {
            $customerId = checkAesCustomer($token, $imei);
        }
        
        $response = $category->landingScreen($storeId, $customerId);
    } else if ($postedJson->type == "save_feedback") {
        require_once FEEDBACK;
        $feedback = new feedback ();
        $orderId = $postedJson->orderId;
        $rating = $postedJson->rating;
        $tags = $postedJson->tags;
        $version = $postedJson->version;
        $platform = $postedJson->platform;
        $token = $postedJson->customerId;
        $customerId = '';
        if($rating < 1) {
            $arr = array(
                "status" => "0",
                "message" => "Please select rating."
                );
            echo $response = json_encode($arr);
            exit;
        }
        
        if(empty($orderId)) {
            $arr = array(
                "status" => "0",
                "message" => "Please give correct order id"
                );
            echo $response = json_encode($arr);
            exit;
        }
        
        if(!empty($token)) {
            $customerId = checkAesCustomer($token, $imei);
        }
        $response = $feedback->saveFeedback($orderId, $customerId, $rating, $tags, $platform, $version);
    }

    //Mage::log("Response -  " . $response, null, 'mobile_api.log');
    echo $response;
}
/*
 * else {
 *
 * }
 */

if (isset($_POST ['type']) == "updateProfileImage") {
    include '../api_config_new.php';
    $token = $_POST['customerId'];
    $customerId = checkAesCustomer($token, $imei);
    if (!empty($customerId)) {
        $customerData = $proxy->call($sessionId, 'customer.info', $customerId);
        $profile_image = $customerData['sstech_profileimage'];

        $target_dir = '../../media/customer/';
        $result = Uploaded_profileimage($profile_image, $target_dir);

        $result1 = explode("_", $result);
        if ($result1[0] == "Done") {
            $result = $proxy->call($sessionId, 'customer.update', array(
                'customerId' => $customerId,
                'customerData' => array(
                    'sstech_profileimage' => $result1[1]
                )
                    ));
            $arr = array(
                "status" => "1",
                "message" => "Profile Image Uploaded Successfully",
                "path" => $result1[1],
            );
            $response = json_encode($arr);
        } else {
            $arr = array(
                "status" => "0",
                "message" => $result,
            );
            $response = json_encode($arr);
        }
    } else {
        $arr = array(
            "status" => "0",
            "message" => $message
        );
        $response = json_encode($arr);
    }
    echo $response;
}

if (isset($_POST ['type']) == "uploadImage") {
    $target_dir = '../../media/userimage/';
    $profile_image = '';
    $result = Uploaded_profileimage($profile_image, $target_dir);

    $result1 = explode("_", $result);
    if ($result1[0] == "Done") {
        $arr = array(
            "status" => "1",
            "message" => "Image Uploaded Successfully",
        );
        $response = json_encode($arr);
    } else {
        $arr = array(
            "status" => "0",
            "message" => $result,
        );
        $response = json_encode($arr);
    }
    echo $response;
}

function Uploaded_profileimage($profile_image, $target_dir) {
    error_reporting(0);
    $media_url = $target_dir . $profile_image;
    define("MAX_SIZE", "1024");

    $errors = 0;

    $image = $_FILES ["file"] ["name"];
    $uploadedfile = $_FILES ['file'] ['tmp_name'];

    if ($image) {
        $filename = stripslashes($_FILES ['file'] ['name']);
        $extension = getExtension($filename);
        $extension = strtolower($extension);
        if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
            $msg = ' Unknown Image extension ';
            $errors = 1;
            return $msg;
        } else {
            $size = filesize($_FILES ['file'] ['tmp_name']);

            if ($size > MAX_SIZE * 1024) {
                $msg = "You have exceeded the size limit";
                $errors = 1;
                return $msg;
            }

            if ($extension == "jpg" || $extension == "jpeg") {
                $uploadedfile = $_FILES ['file'] ['tmp_name'];
                $src = imagecreatefromjpeg($uploadedfile);
            } else if ($extension == "png") {
                $uploadedfile = $_FILES ['file'] ['tmp_name'];
                $src = imagecreatefrompng($uploadedfile);
            } else {
                $src = imagecreatefromgif($uploadedfile);
            }

            list ( $width, $height ) = getimagesize($uploadedfile);

            $newwidth = 960;
            $newheight = ($height / $width) * $newwidth;
            $tmp = imagecreatetruecolor($newwidth, $newheight);

            $newwidth1 = 25;
            $newheight1 = ($height / $width) * $newwidth1;
            $tmp1 = imagecreatetruecolor($newwidth1, $newheight1);

            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            // imagecopyresampled ( $tmp1, $src, 0, 0, 0, 0, $newwidth1, $newheight1, $width, $height );
            $temp = explode(".", $_FILES ["file"] ["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            $filename = $target_dir . $newfilename;
            // $filename1 = "../../media/customer/small" . $_FILES ['file'] ['name'];

            if (file_exists($media_url)) {
                unlink($media_url);
            }
            imagejpeg($tmp, $filename, 100);
            //imagejpeg ( $tmp1, $filename1, 100 );

            imagedestroy($src);
            imagedestroy($tmp);
            imagedestroy($tmp1);
            $newfilename = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "customer/" . $newfilename;
            return "Done_" . $newfilename;
        }
    } else {
        return $msg = "Some Error in Profile image upload.";
    }
    // If no errors registred, print the success message
}

function getExtension($str) {
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }
    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

function checkAesCustomer($token, $imei) {
    //return 17539;
    if (strlen($token) <= 8) {
        $customerId = $token;
    } else {
        $aes = new aesc();
        $customerId = $aes->aescall($token, $imei);
    }
    return $customerId;
}

function emu_getallheaders() {
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[$name] = $value;
        } else if ($name == "CONTENT_TYPE") {
            $headers["Content-Type"] = $value;
        } else if ($name == "CONTENT_LENGTH") {
            $headers["Content-Length"] = $value;
        }
    }
    return $headers;
}

?>