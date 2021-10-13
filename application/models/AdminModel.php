<?php if (!defined('BASEPATH')) exit('No direct script allowed');

class AdminModel extends CI_Model
{

	protected $table;
	public function __construct()
	{
		$this->table = "admin";
	}

	public function get_where($where)
	{
		return $this->db->get_where($this->table, $where);
	}
}
