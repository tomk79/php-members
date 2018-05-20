<?php
/**
 * tomk79/members
 *
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
namespace tomk79\members;

/**
 * members.php
 */
class members{

	/** データベースアクセスオブジェクト */
	private $dba;

	/**
	 * Constructor
	 *
	 * @param object $db_optionsmain データベース設定オブジェクト
	 */
	public function __construct( $db_options ){
		$db_options = json_decode( json_encode($db_options) );
		$this->dba = new dba($db_options);
	}

	/**
	 * メンバー情報を取得する
	 * 
	 * @param string $account メンバーアカウント名
	 */
	public function get_member($account){
		$member = (new member($this, $this->dba))->load_by_account($account);
		return $member;
	}
}
