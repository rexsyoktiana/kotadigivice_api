<?php if (!defined('BASEPATH')) exit('No direct script allowed');

class StudioModel extends CI_Model
{

	protected $table;
	public function __construct()
	{
		$this->table = "studio";
	}

	public function add($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function edit($where, $data)
	{
		$this->db->where($where);
		return $this->db->update($this->table, $data);
	}

	public function delete($where)
	{
		return $this->db->delete($this->table, $where);
	}

	public function get()
	{
		return $this->db->get($this->table);
	}

	public function get_where($where)
	{
		return $this->db->get_where($this->table, $where);
	}
}
