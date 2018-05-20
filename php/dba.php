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

	/** メンバーデータベース(CSV使用時のみセットされます) */
	private $member_db;

	/** ファイルシステム */
	private $fs;

	/** ユーティリティ */
	private $utils;

	/**
	 * Constructor
	 *
	 * @param object $db_options データベース設定オブジェクト
	 * @param object $utils ユーティリティオブジェクト
	 */
	public function __construct( $db_options, $utils ){
		$this->db_options = $db_options;
		$this->fs = new \tomk79\filesystem();
		$this->utils = $utils;

		if( $this->db_options->dbms == 'csv' ){
			if( @is_file($this->db_options->path) ){
				// CSVファイルが定義済みの場合、
				// 予めロードして扱う
				$csv2json = new \tomk79\csv2json( $this->db_options->path );
				$member_db = array();
				foreach($csv2json->fetch_assoc() as $row){
					$member_db[$row['account']] = $row;
				}
				$this->member_db = $member_db;
			}
		}
	}

	/**
	 * データベースを初期化する
	 * @return boolean 成否
	 */
	public function init(){
		if( $this->db_options->dbms == 'csv' ){
			if( !is_dir(dirname($this->db_options->path)) ){
				// ディレクトリが存在しない場合、CSVファイルを生成できない
				return false;
			}
			if( !is_file($this->db_options->path) && !is_writable(dirname($this->db_options->path)) ){
				// CSVファイルは存在しないば、ディレクトリに書き込みができない場合、CSVファイルを生成できない
				return false;
			}
			if( !is_file($this->db_options->path) && (is_readable($this->db_options->path) || is_writable($this->db_options->path)) ){
				// CSVファイルに書き込みまたは読み込みができない場合、CSVファイルを初期化できない
				return false;
			}
			if( is_file($this->db_options->path) && filesize($this->db_options->path) ){
				// 既に内容のあるCSVファイルが存在する場合、定義済みとみなす
				return true;
			}
			$csv = array();
			array_push($csv, array(
				'account'=>'account',
				'name'=>'name',
				'password'=>'password',
				'email'=>'email',
				'auth_level'=>'auth_level',
			));
			$csv_bin = $this->fs->mk_csv($csv);
			return $this->fs->save_file($this->db_options->path, $csv_bin);

		}elseif( $this->db_options->dbms == 'pdo' ){
			ob_start();
?>
CREATE TABLE <?= $this->utils->table_physical_name('members') ?>(
	id             INTEGER PRIMARY KEY AUTOINCREMENT,
	account        TEXT UNIQUE,
	name           TEXT,
	password       TEXT,
	email          TEXT,
	auth_level     INTEGER
);
<?php
			$result = @$this->db_options->pdo->query(ob_get_clean());

		}else{
			// 未対応のDBMS
			return false;
		}
		return true;
	}

	/**
	 * IDから行の情報を取得する
	 * 
	 * @param string $id メンバーID
	 */
	public function get($id){
		if(!is_array( @$this->member_db[$id] )){
			return false;
		}
		return $this->member_db[$id];
	}

	/**
	 * アカウント名から行の情報を取得する
	 * 
	 * @param string $account メンバーアカウント名
	 */
	public function get_by_account($account){
		if(!is_array( @$this->member_db[$account] )){
			return false;
		}
		return $this->member_db[$account];
	}

}
