<?php

class CConnect
{
	private $m_Login;
	private $m_Passwd;
	
	public function __construct ($_connect)
	{
		$this->m_Login  = $_connect['LOGIN'];
		$this->m_Passwd = $_connect['PASS'];
		
	} // __construct()
	
	public function __destruct () { return; }
	
	public function TestLogin ()
	{
		if ($this->m_Login == 'root' &&
		    $this->m_Passwd == 'toor')
			return true;
		else return false;

	} // TestLogin()
	
} // CConnect

?>