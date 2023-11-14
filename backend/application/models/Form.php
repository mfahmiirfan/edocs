<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Form extends CI_Model
{

    private $_table = 'sli_edocs_form';

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('encryption_helper');
    }
    public function findAll($params = [])
    {
        $query = $this->db->get_where($this->_table, $params);
        return $query->result_array();
    }

    public function findAllByFilter($data = [])
    {
        //today
        $datetime = new DateTime();
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime->setTimezone($timezone);

        // echo $datetime->format('Y-m-d H:i:s');exit;
        $where = [
            'sli_edocs_company_id' => $data['company_id']
        ];
        if (isset($data['created_date_from'])) {
            $where['cast(sli_edocs_form_created_date as date) >='] = $data['created_date_from'];
        }
        if (isset($data['created_date_to'])) {
            $where['cast(sli_edocs_form_created_date as date) <='] = $data['created_date_to'];
        }
        if (isset($data['expired_date_from'])) {
            $where['sli_edocs_form_valid_until_date >='] = $data['expired_date_from'];
        }
        if (isset($data['expired_date_to'])) {
            $where['sli_edocs_form_valid_until_date <='] = $data['expired_date_to'];
        }
        $customFilter = [];
        if (isset($data['custom_filter'])) {
            switch ($data['custom_filter']) {
                case "SON":
                    $customFilter['sli_edocs_form_valid_until_date >='] = $datetime->format('Y-m-d');
                    $customFilter['sli_edocs_form_valid_until_date <='] = $datetime->modify('+90 days')->format('Y-m-d');
                    break;
                case "EXP":
                    $customFilter['sli_edocs_form_valid_until_date <'] = $datetime->format('Y-m-d');
                    break;
                case "LON":
                    $customFilter['sli_edocs_form_valid_until_date >'] = $datetime->modify('+90 days')->format('Y-m-d');
                    $customFilter['sli_edocs_form_valid_until_date <>'] = '9999-12-31';
                    break;
                case "NOX":
                    $customFilter['sli_edocs_form_valid_until_date'] = '9999-12-31';
                    break;
            }
        }
        $query = $this->db->from("$this->_table doc")
            ->join('sli_edocs_form_role role', "role.sli_edocs_form_id = doc.sli_edocs_form_id 
        and role.sli_edocs_department_id = $data[department_id]")
            ->where($where)
            ->where($customFilter)->get();
        // echo $this->db->last_query();exit;

        return $query->result_array();
    }

    public function findAllPaginated($params = [])
    {
        $limit = isset($params['limit']) ? $params['limit'] : 10;

        $where = "and d.sli_edocs_company_id = '$params[company_id]'";
        array_walk($params, function ($v, $k) use (&$where) {
            if (in_array($k, ['sli_edocs_form_code', 'sli_edocs_form_name', 'sli_edocs_form_file'])) {
                $V = strtoupper($v);
                $where .= "and upper(d.$k) like '%$V%' ";
            }
        });
        array_walk($params, function ($v, $k) use (&$where) {
            if (in_array($k, ['sli_edocs_form_created_date', 'sli_edocs_form_valid_until_date'])) {
                $where .= "and convert(varchar,d.$k,20) like '%$v%' ";
            }
        });


        $PAGE_SHOW = 5;

        $currPage = null;
        $current10 = null;
        $next5Ids = [];
        $nextId = null;
        $prev5Ids = [];
        $prevId = null;
        if (isset($params['id']) && isset($params['direction']) && isset($params['page'])) {
            if ($params['direction'] == 1) {
                $currPage = $params['page'];

                $query = $this->db->query("select d.* from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id <= $params[id] $where order by d.sli_edocs_form_id desc OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id <= $params[id] $where order by d.sli_edocs_form_id desc OFFSET $nextOffset ROWS FETCH NEXT $nextLimit ROWS ONLY");
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
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id > $params[id] $where order by d.sli_edocs_form_id asc offset $prevOffset rows fetch next $prevLimit rows only");
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
            } elseif ($params['direction'] == -1) {
                $currPage = $params['page'];

                $query = $this->db->query("select d.* from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id < $params[id] $where order by d.sli_edocs_form_id desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id < $params[id] $where order by d.sli_edocs_form_id desc offset $nextOffset rows fetch next $nextLimit rows only");
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
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id >= $params[id] $where order by d.sli_edocs_form_id asc offset $prevOffset rows fetch next $prevLimit rows only");
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
        } else if (isset($params['id']) && isset($params['direction'])) {
            if ($params['direction'] == 1) {
                $query = $this->db->query("select d.* from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id <= $params[id] $where order by d.sli_edocs_form_id desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();


                $nextOffset = $limit;
                $nextLimit = $limit + 1;
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id <= $params[id] $where order by d.sli_edocs_form_id desc offset $nextOffset rows fetch next $nextLimit rows only");
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
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id > $params[id] $where order by d.sli_edocs_form_id asc offset $prevOffset rows fetch next $prevLimit rows only");
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
            } elseif ($params['direction'] == -1) {
                $query = $this->db->query("select d.* from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id < $params[id] $where order by d.sli_edocs_form_id desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit + 1;
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id < $params[id] $where order by d.sli_edocs_form_id desc offset $nextOffset rows fetch next $nextLimit rows only");
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
                $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where d.sli_edocs_form_id >= $params[id] $where order by d.sli_edocs_form_id asc offset $prevOffset rows fetch next $prevLimit rows only");
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
        } else if (isset($params['id']) && $params['id'] == 'LAST') {
            $currPage = 'LAST';
            $query = $this->db->query("select * from(select d.* from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where 1=1 $where order by d.sli_edocs_form_id ASC offset 0 rows fetch next $limit rows only)s order by sli_edocs_form_id desc");
            $current10 = $query->result_array();

            $prevOffset = $limit * 2;
            $prevLimit = $limit + 1;
            $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where 1=1 $where order by d.sli_edocs_form_id asc offset $prevOffset rows fetch next $prevLimit rows only");
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
            $query = $this->db->query("select d.* from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where 1=1 $where order by d.sli_edocs_form_id desc offset 0 rows fetch next $limit rows only");
            $current10 = $query->result_array();

            $offset = $limit;
            $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
            $query = $this->db->query("select d.sli_edocs_form_id id from sli_edocs_form d join sli_edocs_form_role r on r.sli_edocs_form_id = d.sli_edocs_form_id and r.sli_edocs_department_id = $params[department_id] where 1=1 $where order by d.sli_edocs_form_id desc offset $offset rows fetch next $nextLimit rows only");
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

        // echo $this->db->last_query();exit;

        foreach ($current10 as $i => $record) {
            $current10[$i]['sli_edocs_form_id'] = encryptID($record['sli_edocs_form_id']);
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
        $query = $this->db->get_where($this->_table, ['sli_edocs_form_id' => $id]);
        return $query->row_array();
    }

    public function getDepartmentsByDocId($documentId)
    {
        $query = $this->db->from('sli_edocs_form_role role')
            ->join('sli_edocs_department dept', 'dept.sli_edocs_department_id=role.sli_edocs_department_id')
            ->where('role.sli_edocs_form_id', $documentId)
            ->select('role.sli_edocs_department_id, dept.sli_edocs_department_name')
            ->get();

        return $query->result_array();
    }
    public function deleteDepartmentsByDocId($documentId)
    {
        return $this->db->delete('sli_edocs_form_role', array('sli_edocs_form_id' => $documentId));
    }

    public function save($data)
    {
        $query = $this->db->insert($this->_table, [
            'sli_edocs_company_id' => $data['company_id'],
            'sli_edocs_form_code' => $data['sli_edocs_form_code'],
            'sli_edocs_form_name' => $data['sli_edocs_form_name'],
            'sli_edocs_form_file' => $data['sli_edocs_form_file'],
            'sli_edocs_form_valid_until_date' => $data['sli_edocs_form_valid_until_date'],
            'sli_edocs_form_dept_owner' => $data['sli_edocs_department_id'][0],
            'sli_edocs_form_email_to' => isset($data['email_to']) ? str_replace(' ', '', $data['email_to']) : '',
            'sli_edocs_form_email_cc' => isset($data['email_cc']) ? str_replace(' ', '', $data['email_cc']) : ''
        ]);

        if ($query) {
            $insertedId = $this->db->insert_id();

            foreach ($data['sli_edocs_department_id'] as $deptId) {
                $this->db->insert('sli_edocs_form_role', [
                    'sli_edocs_form_id' => $insertedId,
                    'sli_edocs_department_id' => $deptId
                ]);
            }

            //insert log
            $this->saveLog($insertedId, $data['user_id'], 'INSERT');

            //today
            $datetime = new DateTime();
            $timezone = new DateTimeZone('Asia/Jakarta');
            $datetime->setTimezone($timezone);
            $this->saveIndex(
                $insertedId,
                $data['sli_edocs_form_code'],
                $data['sli_edocs_form_name'],
                $datetime,
                $datetime->createFromFormat('Y-m-d', $data['sli_edocs_form_valid_until_date']),
                $data['email_to'],
                $data['email_cc']
            );

            return true;
        }
    }

    public function update($id, $data)
    {
        $deletedDept = isset($data['deleted_dept']) ? json_decode($data['deleted_dept']) : [];
        $addedDept = isset($data['added_dept']) ? json_decode($data['added_dept']) : [];
        $userId = isset($data['user_id']) ? $data['user_id'] : null;

        if (isset($data['email_to'])) {
            $data['sli_edocs_form_email_to'] = str_replace(' ', '', $data['email_to']);
        }
        if (isset($data['email_cc'])) {
            $data['sli_edocs_form_email_cc'] = str_replace(' ', '', $data['email_cc']);
        }
        unset($data['deleted_dept']);
        unset($data['added_dept']);
        unset($data['user_id']);
        unset($data['email_to']);
        unset($data['email_cc']);

        if (!empty($data)) {
            $query = $this->db->update($this->_table, $data, array('sli_edocs_form_id' => $id));
        }

        if (!empty($deletedDept)) {
            foreach ($deletedDept as $deptId) {
                $this->db->delete('sli_edocs_form_role', [
                    'sli_edocs_department_id' => $deptId,
                    'sli_edocs_form_id' => $id
                ]);
            }
        }
        if (!empty($addedDept)) {
            foreach ($addedDept as $deptId) {
                $this->db->insert('sli_edocs_form_role', [
                    'sli_edocs_form_id' => $id,
                    'sli_edocs_department_id' => $deptId
                ]);
            }
        }

        //insert log
        $this->saveLog($id, $userId, 'EDIT');
        //today
        $datetime = new DateTime();
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime->setTimezone($timezone);
        $updated = $this->find($id);
        $this->saveIndex(
            $id,
            $updated['sli_edocs_form_code'],
            $updated['sli_edocs_form_name'],
            $datetime,
            $datetime->createFromFormat('Y-m-d', $updated['sli_edocs_form_valid_until_date']),
            $updated['sli_edocs_form_email_to'],
            $updated['sli_edocs_form_email_cc']
        );
        return true;
    }

    public function destroy($id, $userId)
    {
        if ($this->db->delete($this->_table, array('sli_edocs_form_id' => $id))) {
            //insert log
            $this->saveLog($id, $userId, 'DELETE');
            $this->deleteIndex($id);
            return true;
        }
    }

    public function saveLog($id, $userId, $action)
    {
        $this->db->insert('sli_edocs_form_log', [
            'sli_edocs_form_id' =>  $id,
            'sli_edocs_users_id' => $userId,
            'sli_edocs_form_log_desc' => $action
        ]);
    }
    public function saveIndex($id, $docCode, $docName, $createdDate, $validUntil, $emailTo = '', $emailCc = '')
    {
        // Elasticsearch server details
        $elasticsearchUrl = config_item('base_es_url') . "/edocs_form/_doc/$id";

        // Basic authorization credentials
       $username = config_item('es_username');
        $password= config_item('es_password');

        // Path to the self-signed certificate
        $certificatePath = ROOT_PATH . '/ca.crt';

        // Data to insert into Elasticsearch
        $departments = $this->getDepartmentsByDocId($id);
        $departmentIds = array_map(function ($item) {
            return ['id' => $item['sli_edocs_department_id']];
        }, $departments);

        // Setup request to send json via POST
        $encryptedId = encryptID($id);
        $data = array(
            'doc_code' => $docCode,
            'doc_name' => $docName,
            'departments' => $departmentIds,
            '_view' => base_url() . "backend/form/$encryptedId/preview",
            '_download' => base_url() . "backend/form/$encryptedId/download",
            'created_at' => str_replace('+07:00', 'Z', $createdDate->format('c')),
            'valid_until' => str_replace('+07:00', 'Z', $validUntil->setTime(0, 0, 0)->format('c')),
            'email_to' => $emailTo,
            'email_cc' => $emailCc
        );
        $jsonData = json_encode($data);

        // cURL initialization
        $ch = curl_init($elasticsearchUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Disables SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Disables SSL host verification
        curl_setopt($ch, CURLOPT_CAINFO, $certificatePath); // Set path to the self-signed certificate
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Sets basic authorization credentials

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            $error = curl_error($ch);
            // Handle the error accordingly
        }

        // Close the cURL handle
        curl_close($ch);
    }

    public function deleteIndex($id)
    {
        $elasticsearchUrl = config_item('base_es_url') . "/edocs_form/_doc/$id";
       $username = config_item('es_username');
        $password= config_item('es_password');
        $certificatePath = ROOT_PATH . '/ca.crt';

        // cURL initialization
        $ch = curl_init($elasticsearchUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Enable SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Enable SSL host verification
        curl_setopt($ch, CURLOPT_CAINFO, $certificatePath); // Set path to the self-signed certificate
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Sets basic authorization credentials

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            $error = curl_error($ch);
            // Handle the error accordingly
        } else {
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

        // Close the cURL handle
        curl_close($ch);
    }

    public function initiate()
    {
        //today
        $datetime = new DateTime();
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime->setTimezone($timezone);

        //  var_dump($this->db->get($this->_table)->result_array());exit;

        foreach ($this->db->get($this->_table)->result_array() as $row) {
            $this->saveIndex(
                $row['sli_edocs_form_id'],
                $row['sli_edocs_form_code'],
                $row['sli_edocs_form_name'],
                $datetime->createFromFormat('Y-m-d H:i:s.u', $row['sli_edocs_form_created_date']),
                $datetime->createFromFormat('Y-m-d', $row['sli_edocs_form_valid_until_date']),
                $row['sli_edocs_form_email_to'],
                $row['sli_edocs_form_email_cc']
            );
        }

        return true;
    }
}
