<?php

/*
	
	Shopping cart class

	usage   : Static
	version : 1
	author  : Nick Sheffield

*/

session_start();

class Cart{

	/*

		Add a product to the cart.

		$id         Int   id of the product from the database
		$quantity   Int   quantity the user wants to order

	*/
	public static function add_product($id, $quantity){
		self::create_cart();
		$_SESSION['cart'][$id] = $quantity;
	}




	public static function remove_product($id){
		self::create_cart();
		unset($_SESSION['cart'][$id]);
	}




	public static function increase_quantity($id, $amount){
		self::create_cart();
		$_SESSION['cart'][$id] += $quantity;
	}




	public static function decrease_quantity($id, $amount){
		self::create_cart();
		$_SESSION['cart'][$id] -= $quantity;
	}




	public static function get_cart(){
		self::create_cart();
		return $_SESSION['cart'];
	}




	/* Create the cart array if it doesn't already exist */
	private static function create_cart(){
		if(!isset($_SESSION['cart'])){
			$_SESSION['cart'] = array();
		}
	}

}