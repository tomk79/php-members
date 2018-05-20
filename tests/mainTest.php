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
	 * @dataProvider provideDbSettingOptions
	 */
	public function testCsv( $db_setting ){
		$members = new \tomk79\members\members($db_setting);
		$this->assertTrue( is_object($members) );
		
		// データベースを初期化
		$this->assertTrue( $members->init() );

		// 新メンバー admin を追加
		$id = $members->create_new_member(
			'admin',
			'Administrator',
			'password',
			'admin@example.com'
		);

		// admin のメンバー情報を取得する
		// $user_admin = $members->get_member('admin');
		// $this->assertTrue( is_object($user_admin) );
		// $this->assertEquals( $user_admin->get_id(), 'admin' );
		// $this->assertEquals( $user_admin->get_account(), 'admin' );
		// $this->assertEquals( $user_admin->get_name(), 'Administrator' );
		// $this->assertEquals( $user_admin->get_email(), 'admin@example.com' );
		// $this->assertEquals( $user_admin->get_auth_level(), '100' );
		// $this->assertTrue( $user_admin->verify_password('password') );
	}

	/**
	 * データベース設定のパターン
	 */
	public function provideDbSettingOptions(){
		$fs = new \tomk79\filesystem();
		$fs->save_file( __DIR__.'/testdata/csv/members.csv', '' );
		$fs->save_file( __DIR__.'/testdata/sqlite/members.sqlite', '' );

		$data = array();

		// CSV
		array_push( $data, array( array(
			'dbms' => 'csv',
			'path'=>__DIR__.'/testdata/csv/members.csv',
		) ) );

		// pdo
		$pdo_sqlite = new \PDO(
			'sqlite:'.__DIR__.'/testdata/sqlite/members.sqlite',
			null, null,
			array(
				\PDO::ATTR_PERSISTENT => false, // ←これをtrueにすると、"持続的な接続" になる
			)
		);
		array_push( $data, array( array(
			'dbms' => 'pdo',
			'pdo'=>$pdo_sqlite,
			'prefix'=>'test',
		) ) );

		return $data;
	}
}
