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

require_once 'config.lib.php';
require_once 'collection.lib.php';
require_once 'auth.lib.php';

class Cart{
	
	public static $products = [];

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
		
		if(!$id) return;

		if(isset($_SESSION[Config::$sitename]['cart'][Auth::user_id()][$id])){
			$_SESSION[Config::$sitename]['cart'][Auth::user_id()][$id] += intval($qty);
		}else{
			$_SESSION[Config::$sitename]['cart'][Auth::user_id()][$id] = intval($qty);
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
		unset($_SESSION[Config::$sitename]['cart'][Auth::user_id()][$id]);
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
		
		if(!$id) return;

		$_SESSION[Config::$sitename]['cart'][Auth::user_id()][$id] = intval($qty);
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

		foreach(self::$products as $product){
			$amount += $product->quantity;
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
		return $_SESSION[Config::$sitename]['cart'][Auth::user_id()];
	}


	/**
	*
	*	Clear the cart
	*
	**/
	public static function clear_cart(){
		$_SESSION[Config::$sitename]['cart'][Auth::user_id()] = array();
	}
	
	
	
	public static function products(){
		return self::$products;
	}
	
	
	public static function load_products(){
		if(!Config::$cart_table) return;
		
		# create a collection for the products table
		$col = new Collection(Config::$cart_table);
		
		# loop through the products in the cart
		foreach(self::get_cart() as $id => $qty){
			# select those products by their ids
			# field, value, use_quotes, or
			$col->where('id', $id, true, true);
		}
		
		# if there is any products in the cart at all
		if($_SESSION[Config::$sitename]['cart'][Auth::user_id()]){
			# then load them all into the collection
			$col->get();
		}
		
		# clear the static products array
		self::$products = [];
		
		# for each item in the collection
		foreach($col->items as $product){
			
			# add a quantity property to the model
			$product->quantity = $_SESSION[Config::$sitename]['cart'][Auth::user_id()][$product->id];
			
			# add the product object into the products array
			self::$products[] = $product;
		}
	}



	/**
	*
	*	Create the cart array if it doesn't already exist
	*
	**/
	private static function create_cart(){
		if(!isset($_SESSION[Config::$sitename]['cart'][Auth::user_id()])){
			$_SESSION[Config::$sitename]['cart'][Auth::user_id()] = array();
		}
	}

}


Cart::load_products();