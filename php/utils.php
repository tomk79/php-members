<?php
/**
 * tomk79/members
 *
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
namespace tomk79\members;

/**
 * utils.php
 */
class utils{

	/** データベース設定オブジェクト */
	private $db_options;

	/**
	 * Constructor
	 * @param object $db_optionsmain データベース設定オブジェクト
	 */
	public function __construct( $db_options ){
		$this->db_options = $db_options;
	}

	/**
	 * データベーステーブルの物理名を取得する
	 * プレフィックスの設定を反映した物理名を返します。
	 * @param string $name テーブル名
	 */
	public function table_physical_name($name){
		$name = trim($name);
		if( @strlen( $this->db_options->prefix ) ){
			return trim($this->db_options->prefix).'_'.$name;
		}
		return $name;
	}

}
