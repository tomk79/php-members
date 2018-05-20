<?php
/**
 * Test for tomk79\members
 */

class mainTest extends PHPUnit_Framework_TestCase{

	/**
	 * setup
	 */
	public function setup(){
	}

	/**
	 * CSV
	 */
	public function testCsv(){
		$members = new \tomk79\members\members(array(
			'dbms' => 'csv',
			'path'=>__DIR__.'/testdata/csv/user.csv',
		));
		$this->assertTrue( is_object($members) );
		$user_admin = $members->get_member('admin');
		$this->assertTrue( is_object($user_admin) );
		$this->assertEquals( $user_admin->get_id(), 'admin' );
		$this->assertEquals( $user_admin->get_account(), 'admin' );
		$this->assertEquals( $user_admin->get_name(), 'Administrator' );
		$this->assertEquals( $user_admin->get_email(), 'admin@example.com' );
		$this->assertEquals( $user_admin->get_auth_level(), '100' );
		$this->assertTrue( $user_admin->verify_password('password') );
	}

}
