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
	 * 新しいメンバーを追加する
	 * @return mixed 成功した場合、追加されたメンバーのIDを返します。失敗した場合は `false` を返します。
	 * メンバーのIDは、一般的なDBMSでは連番になり、CSVでは `account` と同じ文字列が返されます。
	 */
	public function create_new_member($account, $name){
		if( $this->db_options->dbms == 'csv' ){
			if( !@is_null($this->member_db[$account]) ){
				// 既にアカウントが存在する場合
				return false;
			}
			$this->member_db[$account] = array(
				'account'=>$account,
				'name'=>$name,
				'password'=>null,
				'email'=>null,
				'auth_level'=>0,
			);
			$result = $this->save_member_db($this->member_db);
			if(!$result){
				return false;
			}
			return $account;

		}elseif( $this->db_options->dbms == 'pdo' ){
			ob_start();
?>
INSERT INTO <?= $this->utils->table_physical_name('members') ?>(
	account,
	name,
	password,
	email,
	auth_level
)VALUES(
	:account,
	:name,
	null,
	null,
	0
);
<?php
			$sth = $this->db_options->pdo->prepare( ob_get_clean() );
			$sth->execute(array(
				':account'=>$account,
				':name'=>$name,
			));
			$rtn = $this->db_options->pdo->lastInsertId('id');
			return $rtn;

		}else{
			// 未対応のDBMS
			return false;
		}
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


	/**
	 * $member_db を保存する
	 * dbmsにCSVを選択した場合のみ利用可能です。
	 * @return boolean 成否。 CSV以外のDBMSの場合は、常に `false`。
	 */
	private function save_member_db(){
		if( $this->db_options->dbms == 'csv' ){
			$csv = array();
			array_push($csv, array(
				'account'=>'account',
				'name'=>'name',
				'password'=>'password',
				'email'=>'email',
				'auth_level'=>'auth_level',
			));
			foreach( $this->member_db as $account=>$member_info ){
				array_push($csv, array(
					'account'=>$account,
					'name'=>$member_info['name'],
					'password'=>$member_info['password'],
					'email'=>$member_info['email'],
					'auth_level'=>$member_info['auth_level'],
				));
			}
			$csv_bin = $this->fs->mk_csv($csv);
			return $this->fs->save_file($this->db_options->path, $csv_bin);
		}
		return false;
	}
}
