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
			'path'=>__DIR__.'/testdata/csv/user.csv',
		));
		$this->assertTrue( is_object($members) );
		$user_admin = $members->get_user_info('admin');
		$this->assertTrue( is_array($user_admin) );
		$this->assertEquals( $user_admin['account'], 'admin' );
		$this->assertEquals( $user_admin['name'], 'Administrator' );
		$this->assertEquals( $user_admin['email'], 'admin@example.com' );
		$this->assertEquals( $user_admin['auth_level'], '100' );
	}

}
