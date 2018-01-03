<?php
include('password.php');
class User extends Password{

    private $_db;

    function __construct($db){
    	parent::__construct();

    	$this->_db = $db;
    }

	private function getFalcultyHash($email)
	{

		try {
			$stmt = $this->_db->prepare('SELECT password, email, fid FROM faculty WHERE email = :email AND active="Yes" ');
			$stmt->execute(array('email' => $email));

			return $stmt->fetch();

		} catch(PDOException $e) {
			echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	private function getStudentHash($email)
	{

		try {
			$stmt = $this->_db->prepare('SELECT password, email, sid FROM student WHERE email = :email AND isactive=1 ');
			$stmt->execute(array('email' => $email));

			return $stmt->fetch();

		} catch(PDOException $e) {
			echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	public function isValidUsername($username)
	{
		if (strlen($username) < 3) return false;
		if (strlen($username) > 17) return false;
		if (!ctype_alnum($username)) return false;
		return true;
	}

	public function login($email,$password){
		
		if (strlen($password) < 3) return false;

		$row = $this->getStudentHash($email);

		if (empty($row)) {
			$row = $this->getFalcultyHash($email);
		}
		else{
			$_SESSION["userType"] = "student";
		}

		if($this->password_verify($password,$row['password']) == 1){

			$_SESSION['loggedin'] = true;
			$_SESSION['email'] = $row['email'];

			if ($_SESSION["userType"] === "student") {
				$_SESSION['sid'] = $row['sid'];
			} else {
				$_SESSION['fid'] = $row['fid'];
			}
			return true;
		}
	}

	public function logout(){
		session_destroy();
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
	}

	public function getFacultyName($email)
	{
		try {
			$stmt = $this->_db->prepare('SELECT firstname, lastname FROM faculty WHERE email = :email');
			$stmt->execute(array('email' => $email));

			return $stmt->fetch();

		} catch(PDOException $e) {
			echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	public function getStudentName($email)
	{
		try {
			$stmt = $this->_db->prepare('SELECT firstname, lastname FROM student WHERE email = :email');
			$stmt->execute(array('email' => $email));

			return $stmt->fetch();

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	public function get_user_id($email){
		try {
			$stmt = $this->_db->prepare('SELECT fid FROM faculty WHERE email = :email');
			$stmt->execute(array('email' => $email));

			return $stmt->fetch();

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	public function get_faculty_details($fid){
		try {
			$stmt = $this->_db->prepare('SELECT * FROM faculty WHERE fid = :fid');
			$stmt->execute(array('fid' => $fid));

			return $stmt->fetch();

		} catch(PDOException $e) {
			echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

}


?>
