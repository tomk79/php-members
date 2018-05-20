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

	/** ユーティリティ */
	private $utils;

	/** データベースアクセスオブジェクト */
	private $dba;

	/**
	 * Constructor
	 *
	 * @param object $db_options データベース設定オブジェクト
	 */
	public function __construct( $db_options ){
		$tmp_pdo = null;
		if( @$db_options->pdo ){
			$tmp_pdo = $db_options->pdo;
		}elseif( @$db_options['pdo'] ){
			$tmp_pdo = $db_options['pdo'];
		}
		$db_options = json_decode( json_encode($db_options) );
		$db_options->pdo = $tmp_pdo;

		$this->utils = new utils($db_options);
		$this->dba = new dba($db_options, $this->utils);
	}

	/**
	 * データベースを初期化する
	 */
	public function init(){
		return $this->dba->init();
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
