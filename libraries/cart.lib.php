<?php

/**
*	
*	Shopping cart class.
*	
*	Used in static context.
*
*	@version 1.2
*	@author Nick Sheffield
*
*/

session_start();

class Cart{

	/**
	*
	*	Add a product to the cart.
	*
	*	If the user adds a product that already exists in the cart,
	*	then the quantity of that product is increased.
	*
	*	@param int $id  id of the product from the database
	*	@param int $qty quantity the user wants to order
	*
	**/
	public static function add_product($id, $qty){
		self::create_cart();

		if(isset($_SESSION['cart'][$id])){
			$_SESSION['cart'][$id] += intval($qty);
		}else{
			$_SESSION['cart'][$id] = intval($qty);
		}
		
	}


	/**
	*
	*	Remove a product from the cart
	*
	*	@param int $id The id of the product we want to remove
	*
	**/
	public static function remove_product($id){
		self::create_cart();
		unset($_SESSION['cart'][$id]);
	}


	/**
	*
	*	Remove a product from the cart
	*
	*	@param int $id  The id of the product who's quantity we want to modify
	*	@param int $qty The new quantity amount to set
	*
	**/
	public static function set_quantity($id, $qty){
		self::create_cart();

		$_SESSION['cart'][$id] = intval($qty);
	}


	/**
	*
	*	Get the total quantity of all products in the cart
	*
	*	@return int $amount The total quantity
	*
	**/
	public static function get_total(){
		self::create_cart();

		$amount = 0;

		foreach($_SESSION['cart'] as $quantity){
			$amount += $quantity;
		}

		return $amount;
	}


	/**
	*
	*	Get the cart array
	*
	**/
	public static function get_cart(){
		self::create_cart();
		return $_SESSION['cart'];
	}


	/**
	*
	*	Clear the cart
	*
	**/
	public static function clear_cart(){
		$_SESSION['cart'] = array();
	}



	/**
	*
	*	Create the cart array if it doesn't already exist
	*
	**/
	private static function create_cart(){
		if(!isset($_SESSION['cart'])){
			$_SESSION['cart'] = array();
		}
	}

}