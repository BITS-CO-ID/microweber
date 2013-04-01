﻿<?php

/*

type: layout

name: Small

description: Small cart template

*/
  ?>
<style>
.mw-cart-small {
	display: inline-block;
	float: right;
	margin: 37px 0 0 15px;
	border: 1px solid #C5C2C2;
	border-top-color:#E5E1E1;
	border-bottom-color:#A1A0A0;
	background-color: #EDEDED;
	font-size: 12px;
}
.mw-cart-small-content {
	padding: 1px 3px 1px 6px;
	float: left;
	white-space: nowrap;
}
.mw-cart-small a {
	font-size: 13px;
}
.mw-cart-small strong {
	margin: 0 3px;
}
.mw-cart-small .cart-icon {
	float: left;
	display: block;
	width: 30px;
	text-align: center;
	padding: 4px 0;
	background: white;
	border-right: 1px solid #C5C2C2;
}
.mw-cart-small-order-info {
	position: relative;
	top: 2px;
}
.mw-cart-small .no-items {
	display: inline-block;
	padding: 2px 5px 0 0;
}
</style>
<div class="mw-cart-small  mw-cart-<? print $params['id']?> <? print  $template_css_prefix  ?>"> <span class="cart-icon"><span class="icon-shopping-cart"></span></span>
  <div class="mw-cart-small-content">
    <? if(isarr($data)) :?>
    <?php
        $total_qty = 0;
        $total_price = 0;
        foreach ($data as $item) {
            $total_qty += $item['qty'];
            $total_price +=  $item['price']* $item['qty'];
        }
      ?>
    <span class="mw-cart-small-order-info">Cart (<strong><?php print $total_qty; ?></strong>) <?php print currency_format($total_price); ?></span> |
    <?
  if(!isset($params['checkout-link-enabled'])){
	  $checkout_link_enanbled =  get_option('data-checkout-link-enabled', $params['id']);
  } else {
	   $checkout_link_enanbled = $params['checkout-link-enabled'];
  }
   ?>
    <? if($checkout_link_enanbled != 'n') :?>
    <? $checkout_page =get_option('data-checkout-page', $params['id']); ?>
    <? if($checkout_page != false and strtolower($checkout_page) != 'default' and intval($checkout_page) > 0){

	   $checkout_page_link = content_link($checkout_page).'/view:checkout';
   } else {
	   $checkout_page_link = site_url('checkout');

   }

   ?>
    <a class="btn btn-mini right" href="<? print $checkout_page_link; ?>">Checkout</a>
    <? endif ; ?>
    <? else : ?>
    <span class="no-items">
    <?   _e('Your cart is empty') ?>
    </span>
    <? endif ; ?>
  </div>
</div>