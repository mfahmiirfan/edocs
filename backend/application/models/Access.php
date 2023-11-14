<?php
class Access extends CI_Model
{
        private $_table = 'sli_edocs_users_submenu';

        public function findAll($params = [])
        {
                $query = $this->db->get_where($this->_table, $params);

                return $query->result_array();
        }

        public function find($id)
        {
                $query = $this->db->get_where($this->_table, ['sli_edocs_users_submenu_id' => $id]);
                return $query->row_array();
        }

        public function findAccessByUserId($userId)
        {
                $query = $this->db->from($this->_table)
                        ->join('sli_edocs_submenu page', "page.sli_edocs_submenu_id=$this->_table.sli_edocs_submenu_id")
                        ->where("$this->_table.sli_edocs_users_id", $userId)
                        ->select("$this->_table.*, page.sli_edocs_submenu_name")
                        ->get();
                return $query->result_array();
        }

        public function save($data)
        {
                $this->db->insert($this->_table, $data);
        }

        public function update($id, $data)
        {
                $this->db->update($this->_table, $data, ['sli_edocs_users_submenu_id' => $id]);
        }

        public function destroy($id)
        {
                $this->db->delete($this->_table, ['sli_edocs_users_submenu_id' => $id]);
        }

        
}
