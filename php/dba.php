<?php
/**
 * tomk79/members
 *
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
namespace tomk79\members;

/**
 * dba.php
 */
class dba{

	/** データベース設定オブジェクト */
	private $db_options;

	/**
	 * Constructor
	 *
	 * @param object $db_optionsmain データベース設定オブジェクト
	 */
	public function __construct( $db_options ){
		$this->db_options = $db_options;

		$csv2json = new \tomk79\csv2json( $db_options->path );
		$user_db = array();
		foreach($csv2json->fetch_assoc() as $row){
			$user_db[$row['account']] = $row;
		}
		$this->user_db = $user_db;
		// var_dump($this->user_db);
	}

	/**
	 * IDから行の情報を取得する
	 * 
	 * @param string $id メンバーID
	 */
	public function get($id){
		if(!is_array( @$this->user_db[$id] )){
			return false;
		}
		return $this->user_db[$id];
	}

	/**
	 * アカウント名から行の情報を取得する
	 * 
	 * @param string $account メンバーアカウント名
	 */
	public function get_by_account($account){
		if(!is_array( @$this->user_db[$account] )){
			return false;
		}
		return $this->user_db[$account];
	}
}
