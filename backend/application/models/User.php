<?php
class User extends CI_Model
{

    private $_table = 'sli_edocs_users';

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('encryption_helper');
    }

    public function findAll($params = [])
    {
        $query = $this->db->from($this->_table)
            ->join('sli_edocs_users_role role', "role.sli_edocs_users_role_id=$this->_table.sli_edocs_users_role_id")
            ->join('sli_edocs_company company', "company.sli_edocs_company_id=$this->_table.sli_edocs_company_id")
            ->where($params)
            ->select("$this->_table.*, role.sli_edocs_users_role_name role_name, company.sli_edocs_company_code company_code")
            ->get();

        return $query->row_array();
    }

    public function findAllPaginated($filter = [])
    {
        $limit = isset($filter['limit']) ? $filter['limit'] : 10;

        $where = "and u.sli_edocs_company_id = '$filter[company_id]'";
        array_walk($filter, function ($v, $k) use (&$where) {
            if (in_array($k, ['sli_edocs_users_nik', 'sli_edocs_users_name', 'sli_edocs_users_department', 'sli_edocs_users_email', 'sli_edocs_users_phone', 'sli_edocs_users_role_name'])) {
                $V = strtoupper($v);

                if($k=='sli_edocs_users_role_name'){
                    $where .= "and upper(r.$k) like '%$V%' ";
                    return;
                }
                $where .= "and upper(u.$k) like '%$V%' ";
            }
        });
        // array_walk($filter, function ($v, $k) use (&$where) {
        //     if (in_array($k, ['sli_edocs_integrated_report_created_date','sli_edocs_integrated_report_valid_until_date'])) {
        //         $where .= "and convert(varchar,u.$k,20) like '%$v%' ";
        //     }
        // });

        $PAGE_SHOW = 5;

        $currPage = null;
        $current10 = null;
        $next5Ids = [];
        $nextId = null;
        $prev5Ids = [];
        $prevId = null;
        if (isset($filter['id']) && isset($filter['direction']) && isset($filter['page'])) {
            if ($filter['direction'] == 1) {
                $currPage = $filter['page'];

                $query = $this->db->query("select u.*, d.sli_edocs_department_name, r.sli_edocs_users_role_name from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id <= $filter[id] $where order by u.sli_edocs_users_id desc OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id <= $filter[id] $where order by u.sli_edocs_users_id desc OFFSET $nextOffset ROWS FETCH NEXT $nextLimit ROWS ONLY");
                $next41 = $query->result_array();

                $nextPage = $currPage + 1;
                array_walk($next41, function ($v, $k) use (&$next5Ids, $limit, &$nextPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['page'] = $nextPage;
                        $v['limit'] = $limit;
                        array_push($next5Ids, $v);

                        $nextPage++;
                    }
                });
                $nextId = count($next5Ids) > 0 ? $next5Ids[0] : null;


                $prevOffset = $limit;
                $prevLimit = $limit * $PAGE_SHOW + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id > $filter[id] $where order by u.sli_edocs_users_id asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prevPage = $currPage - 1;
                array_walk($prev51, function ($v, $k) use (&$prev5Ids, $limit, &$prevPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['page'] = $prevPage;
                        $v['limit'] = $limit;
                        array_unshift($prev5Ids, $v);

                        $prevPage--;
                    }
                });
                $prevId = count($prev5Ids) > 0 ? $prev5Ids[count($prev5Ids) - 1] : null;
            } elseif ($filter['direction'] == -1) {
                $currPage = $filter['page'];

                $query = $this->db->query("select u.*, d.sli_edocs_department_name, r.sli_edocs_users_role_name from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id < $filter[id] $where order by u.sli_edocs_users_id desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id < $filter[id] $where order by u.sli_edocs_users_id desc offset $nextOffset rows fetch next $nextLimit rows only");
                $next41 = $query->result_array();

                $nextPage = $currPage + 1;
                array_walk($next41, function ($v, $k) use (&$next5Ids, $limit, &$nextPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['page'] = $nextPage;
                        $v['limit'] = $limit;
                        array_push($next5Ids, $v);

                        $nextPage++;
                    }
                });
                $nextId = count($next5Ids) > 0 ? $next5Ids[0] : null;

                $prevOffset = $limit;
                $prevLimit = $limit * $PAGE_SHOW + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id >= $filter[id] $where order by u.sli_edocs_users_id asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prevPage = $currPage - 1;
                array_walk($prev51, function ($v, $k) use (&$prev5Ids, $limit, &$prevPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['page'] = $prevPage;
                        $v['limit'] = $limit;
                        array_unshift($prev5Ids, $v);

                        $prevPage--;
                    }
                });
                $prevId = count($prev5Ids) > 0 ? $prev5Ids[count($prev5Ids) - 1] : null;
            }
        } else if (isset($filter['id']) && isset($filter['direction'])) {
            if ($filter['direction'] == 1) {
                $query = $this->db->query("select u.*, d.sli_edocs_department_name, r.sli_edocs_users_role_name from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id <= $filter[id] $where order by u.sli_edocs_users_id desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();


                $nextOffset = $limit;
                $nextLimit = $limit + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id <= $filter[id] $where order by u.sli_edocs_users_id desc offset $nextOffset rows fetch next $nextLimit rows only");
                $next41 = $query->result_array();

                $next1Ids = [];
                array_walk($next41, function ($v, $k) use (&$next1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['limit'] = $limit;
                        array_push($next1Ids, $v);
                    }
                });
                $nextId = count($next1Ids) > 0 ? $next1Ids[0] : null;

                $prevOffset = $limit;
                $prevLimit = $limit + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id > $filter[id] $where order by u.sli_edocs_users_id asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prev1Ids = [];
                array_walk($prev51, function ($v, $k) use (&$prev1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['limit'] = $limit;
                        array_unshift($prev1Ids, $v);
                    }
                });
                $prevId = count($prev1Ids) > 0 ? $prev1Ids[count($prev1Ids) - 1] : null;
            } elseif ($filter['direction'] == -1) {
                $query = $this->db->query("select u.*, d.sli_edocs_department_name, r.sli_edocs_users_role_name from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id < $filter[id] $where order by u.sli_edocs_users_id desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id < $filter[id] $where order by u.sli_edocs_users_id desc offset $nextOffset rows fetch next $nextLimit rows only");
                $next41 = $query->result_array();

                $next1Ids = [];
                array_walk($next41, function ($v, $k) use (&$next1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['limit'] = $limit;
                        array_push($next1Ids, $v);
                    }
                });
                $nextId = count($next1Ids) > 0 ? $next1Ids[0] : null;

                $prevOffset = $limit;
                $prevLimit = $limit + 1;
                $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where u.sli_edocs_users_id >= $filter[id] $where order by u.sli_edocs_users_id asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prev1Ids = [];
                array_walk($prev51, function ($v, $k) use (&$prev1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['limit'] = $limit;
                        array_unshift($prev1Ids, $v);
                    }
                });
                $prevId = count($prev1Ids) > 0 ? $prev1Ids[count($prev1Ids) - 1] : null;
            }
        } else if (isset($filter['id']) && $filter['id'] == 'LAST') {
            $currPage = 'LAST';
            $query = $this->db->query("select * from(select u.*, d.sli_edocs_department_name, r.sli_edocs_users_role_name from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where 1=1 $where order by u.sli_edocs_users_id ASC offset 0 rows fetch next $limit rows only)s order by sli_edocs_users_id desc");
            $current10 = $query->result_array();

            $prevOffset = $limit * 2;
            $prevLimit = $limit + 1;
            $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where 1=1 $where order by u.sli_edocs_users_id asc offset $prevOffset rows fetch next $prevLimit rows only");
            $prev11 = $query->result_array();

            $prev1Ids = [];
            array_walk($prev11, function ($v, $k) use (&$prev1Ids, $limit) {
                if ($k % $limit == 0) {
                    $v['direction'] = -1;
                    $v['limit'] = $limit;
                    array_unshift($prev1Ids, $v);
                }
            });
            $prevId = count($prev1Ids) > 0 ? $prev1Ids[count($prev1Ids) - 1] : null;
        } else {
            $currPage = 'FIRST';
            $query = $this->db->query("select u.*, d.sli_edocs_department_name, r.sli_edocs_users_role_name from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where 1=1 $where order by u.sli_edocs_users_id desc offset 0 rows fetch next $limit rows only");
            $current10 = $query->result_array();

            $offset = $limit;
            $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
            $query = $this->db->query("select u.sli_edocs_users_id id from sli_edocs_users u left join sli_edocs_department d on d.sli_edocs_department_id = u.sli_edocs_department_id left join sli_edocs_users_role r on r.sli_edocs_users_role_id = u.sli_edocs_users_role_id where 1=1 $where order by u.sli_edocs_users_id desc offset $offset rows fetch next $nextLimit rows only");
            $next41 = $query->result_array();

            $nextPage = 2;
            array_walk($next41, function ($v, $k) use (&$next5Ids, $limit, &$nextPage) {
                if ($k % $limit == 0) {
                    $v['direction'] = 1;
                    $v['page'] = $nextPage;
                    $v['limit'] = $limit;
                    array_push($next5Ids, $v);

                    $nextPage++;
                }
            });
            $nextId = count($next5Ids) > 0 ? $next5Ids[0] : null;
        }

        foreach ($current10 as $i => $record) {
            $current10[$i]['sli_edocs_users_id'] = encryptID($record['sli_edocs_users_id']);
        }
        return [
            'currPage' => $currPage,
            'current10' => $current10,
            'next5Ids' => $next5Ids,
            'prev5Ids' => $prev5Ids,
            'nextId' => $nextId,
            'prevId' => $prevId
        ];
    }

    public function find($id)
    {
        $query = $this->db->from($this->_table)
            ->join('sli_edocs_users_role role', "role.sli_edocs_users_role_id = $this->_table.sli_edocs_users_role_id")
            ->where("$this->_table.sli_edocs_users_id", $id)
            ->select("$this->_table.*, role.sli_edocs_users_role_name")
            ->get();
        return $query->row_array();
    }

    public function findByDepartments($deptIds)
    {
        $query = $this->db->from($this->_table)
            ->where_in('sli_edocs_department_id', $deptIds)->get();
        return $query->result_array();
    }

    public function save($data)
    {
        $query =  $this->db->insert($this->_table, [
            'sli_edocs_users_nik' => $data['username'],
            'sli_edocs_users_name' => strtoupper($data['name']),
            'sli_edocs_users_password_ci' => password_hash($data['password'], PASSWORD_DEFAULT),
            'sli_edocs_users_real_password_ci' => $data['password'],
            'sli_edocs_users_role_id' => $data['role_id'],
            'sli_edocs_company_id' => $data['company_id'],
            'sli_edocs_department_id' => $data['department_id'],
            'sli_edocs_users_email' => $data['email'],
            'sli_edocs_users_phone' => $data['phone'],
        ]);
        if ($query) {
            $insertedId = $this->db->insert_id();

            foreach ($data['submenu_id'] as $deptId) {
                $this->db->insert('sli_edocs_users_submenu', [
                    'sli_edocs_users_id' => $insertedId,
                    'sli_edocs_submenu_id' => $deptId
                ]);
            }

            //insert log
            $this->saveLog($data['user_id'], "INSERT DATA EMPLOYEE ID $insertedId BY $data[user_name]");
            return true;
        }
    }

    public function update($id, $data)
    {
        $deletedSub = isset($data['deleted_sub']) ? json_decode($data['deleted_sub']) : [];
        $addedSub = isset($data['added_sub']) ? json_decode($data['added_sub']) : [];
        $userId = isset($data['user_id']) ? $data['user_id'] : null;
        $userName = isset($data['user_name']) ? $data['user_name'] : null;
        unset($data['deleted_sub']);
        unset($data['added_sub']);
        unset($data['user_id']);
        unset($data['user_name']);
        unset($data['repassword']);

        if (!empty($data)) {
            if (isset($data['sli_edocs_users_password_ci'])) {
                $data['sli_edocs_users_real_password_ci'] = $data['sli_edocs_users_password_ci'];
                $data['sli_edocs_users_password_ci'] = password_hash($data['sli_edocs_users_password_ci'], PASSWORD_DEFAULT);
            }
            $query = $this->db->update($this->_table, $data, array('sli_edocs_users_id' => $id));
        }
        if (!empty($deletedSub)) {
            foreach ($deletedSub as $subId) {
                $this->db->delete('sli_edocs_users_submenu', [
                    'sli_edocs_submenu_id' => $subId,
                    'sli_edocs_users_id' => $id
                ]);
            }
        }
        if (!empty($addedSub)) {
            foreach ($addedSub as $subId) {
                $this->db->insert('sli_edocs_users_submenu', [
                    'sli_edocs_submenu_id' => $subId,
                    'sli_edocs_users_id' => $id
                ]);
            }
        }

        //insert log
        $this->saveLog($userId, "UPDATE DATA EMPLOYEE ID $id BY $userName");
        return true;
    }

    public function destroy($id, $data)
    {
        if (!$this->db->delete('sli_edocs_users_submenu', ['sli_edocs_users_id' => $id])) {
            return false;
        }

        if ($this->db->delete($this->_table, array('sli_edocs_users_id' => $id))) {
            //insert log
            $this->saveLog($data['user_id'], "DELETE DATA EMPLOYEE ID $id BY $data[user_name]");
            return true;
        }

        return false;
    }

    public function isValid($data)
    {
        $username = $data['username'];
        $password = $data['password'];
        // $company = $data['company_code'];

        if ($this->findAll(['sli_edocs_users_nik' => $username/*,'company_code'=>$company*/])) {
            $hash = $this->findAll(['sli_edocs_users_nik' => $username/*,'company_code'=>$company*/])['sli_edocs_users_password_ci'];
            if (password_verify($password, $hash)) {
                return true;
            }
        }
        return false;
    }

    public function saveLog($userId, $action)
    {
        $this->db->insert('sli_edocs_users_log', [
            'sli_edocs_users_id' => $userId,
            'sli_edocs_users_log_action' => $action
        ]);
    }

    public function getSubmenusByUserId($userId)
    {
        $query = $this->db->from('sli_edocs_users_submenu usub')
            ->join('sli_edocs_submenu sub', 'sub.sli_edocs_submenu_id=usub.sli_edocs_submenu_id')
            ->where(array('usub.sli_edocs_users_id' => $userId))
            ->select('usub.sli_edocs_submenu_id, sub.sli_edocs_submenu_name')
            ->get();

        return $query->result_array();
    }
}
