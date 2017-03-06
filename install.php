<?php
if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );

class Install extends CI_Controller {
	private $_version = '';

	function __construct() {
		// Call the Model constructor
		parent::__construct();
		//$this->model_auth->checkLogin();
	}
	
	function index(){
		$parse_url = parse_url(base_url());
		$check_base_url = $parse_url['host'] == $_SERVER['HTTP_HOST'];
		$database = $this->db->hostname != 'mysql.josimar.net';
		$hasbackupsql = file_exists('./backup/mybackup.sql');
		
		
		echo "
			<html>
			<head>
			<title>SCRIPTBUILDER</title>
				<style>
				 	body { text-align:center;}
					ul {width: 300px; margin: 0 auto;}
					.green { color: #0F0; text-weight: bold;}
					.red { color: #F00; text-weight: bold;}
				</style>
			</head>
			<body>
			<p>Bem vindo ao instalador!</p>
				
				<ul>
					<li class=".($check_base_url == 1? 'green' : 'red').">Configurar a base_url(): ".($check_base_url == 1? 'ok' : '')."</li>
					<li class=".($database == 1? 'green' : 'red').">Banco de dados(): ".($database == 1? 'ok' : '')."</li>
					<li class=".($hasbackupsql != 1? 'green' : 'red').">Arquivo sql não existe: ".($hasbackupsql != 1? 'ok' : '')."</li>
							
				</ul>
							
				";
				
		
		if(!$hasbackupsql){
			echo anchor(base_url('install/backup','Backup'));
		}else{
			echo anchor(base_url('install/restoredb','Backup'));
		}
				
				echo "
			</body>
			</html>
			";
	}

	function restoredb(){

		$isi_file = file_get_contents('./backup/mybackup.sql');
		if( mysqli_multi_query( $this->db->conn_id, $isi_file) ){
			unlink('./backup/mybackup.sql');
			rmdir('./backup');
		}
		redirect(base_url('install'));

	}
	
	function backup(){
		$this->load->dbutil();
		$prefs = array(
				//'tables'     => array('table1', 'table2'),
				'ignore'     => array(),
				'format'     => 'txt',
				'filename'   => 'mybackup.sql',
				'add_drop'   => TRUE,
				'add_insert' => TRUE,
				'newline'    => "\n"
		);
		
		if( !file_exists('./backup')){
			mkdir('./backup');
		}
		
		file_put_contents('./backup/mybackup.sql', $this->dbutil->backup($prefs));
		redirect(base_url('install'));
	}
	
}