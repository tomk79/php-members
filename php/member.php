<?php
/**
 * tomk79/members
 *
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
namespace tomk79\members;

/**
 * member.php
 */
class member{

	/** membersオブジェクト */
	private $members;

	/** データベースアクセスオブジェクト */
	private $dba;

	/** メンバーの情報 */
	private $member_info;

	/**
	 * Constructor
	 *
	 * @param object $members membersオブジェクト
	 * @param object $dba データベースアクセスオブジェクト
	 */
	public function __construct( $members, $dba ){
		$this->members = $members;
		$this->dba = $dba;
	}

	/**
	 * メンバーIDでロードする
	 *
	 * @param object $id メンバーID
	 */
	public function load( $id ){
		$this->member_info = $this->dba->get($id);
		return $this;
	}

	/**
	 * アカウント名でロードする
	 *
	 * @param object $account アカウント名
	 */
	public function load_by_account( $account ){
		$this->member_info = $this->dba->get_by_account($account);
		return $this;
	}

	/**
	 * メンバーIDを取得する
	 */
	public function get_id(){
		// CSVの場合は account と同じ値を返す
		return $this->member_info['account'];
	}

	/**
	 * アカウント名を取得する
	 */
	public function get_account(){
		return $this->member_info['account'];
	}

	/**
	 * メンバーの名前を取得する
	 */
	public function get_name(){
		return $this->member_info['name'];
	}

	/**
	 * メールアドレスを取得する
	 */
	public function get_email(){
		return $this->member_info['email'];
	}

	/**
	 * 権限レベル値を取得する
	 */
	public function get_auth_level(){
		return $this->member_info['auth_level'];
	}

	/**
	 * メンバーのパスワードを照合する
	 */
	public function verify_password( $password ){
		if( $this->member_info['password'] !== sha1($password) ){
			return false;
		}
		return true;
	}

}
