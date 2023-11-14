<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DocumentController extends CI_Controller
{
    public $is_token_verify_hookable=TRUE;
    public function getExpiredSoon()
    {
        $deptId = $this->input->get('dept_id');
        $index = $this->input->get('index');

        $url = config_item('base_es_url').'/_search'; // Replace with your Elasticsearch endpoint and index
       $username = config_item('es_username');
        $password= config_item('es_password');
        $certificatePath = ROOT_PATH . '/ca.crt';
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Disables SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Disables SSL host verification
        curl_setopt($ch, CURLOPT_CAINFO, $certificatePath); // Set path to the self-signed certificate
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Sets basic authorization credentials

        // Remove null values
        $indexWoNull = array_values(array_filter(json_decode($index, true), function ($value) {
            return $value !== null;
        }));

        $query = [
            'size' => 6,
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'valid_until' => [
                                    'lte' => 'now+90d'
                                ]
                            ]
                        ], 
                        [
                            "match" => [
                                "departments.id" => $deptId
                            ]
                        ],
                        [
                            "terms" => [
                                "_index" => $indexWoNull
                            ]
                        ]
                    ]
                ]
            ], 
            "sort" => [
                [
                    "valid_until" => [
                        "order" => "asc"
                    ]
                ]
            ]
        ];
        $jsonData = json_encode($query);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        if ($response === false) {
            // Handle the error
            $error = curl_error($ch);
            curl_close($ch);
            die("cURL request failed: $error");
        }

        //query ke dua
        $query = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'valid_until' => [
                                    'lte' => 'now+90d',
                                    'gt' => 'now/d'
                                ]
                            ]
                        ], 
                        [
                            "match"=>[
                                "departments.id"=>$deptId
                            ]
                        ],
                        [
                            "terms" => [
                                "_index" => $indexWoNull
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $jsonData = json_encode($query);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response2 = curl_exec($ch);
        if ($response2 === false) {
            // Handle the error
            $error = curl_error($ch);
            curl_close($ch);
            die("cURL request failed: $error");
        }
        $result2 = json_decode($response2, true);

        // Handle the successful response
        curl_close($ch);
        $result = json_decode($response, true);
        // Process $result as needed
        if (isset($result['hits']['hits'])) {
            $data = $result['hits']['hits'];
        } else {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'data' => $data,
                'total' => $result['hits']['total']['value'],
                'expire_soon' => $result2['hits']['total']['value']
            ]));
    }

    public function getExpiredSoonPaginated()
    {
        $searchAfter = $this->input->post('search_after');
        $deptId = $this->input->post('department_id');
        $index = $this->input->post('index');

        $url = config_item('base_es_url').'/_search'; // Replace with your Elasticsearch endpoint and index
       $username = config_item('es_username');
        $password= config_item('es_password');
        $certificatePath = ROOT_PATH . '/ca.crt';
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Disables SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Disables SSL host verification
        curl_setopt($ch, CURLOPT_CAINFO, $certificatePath); // Set path to the self-signed certificate
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Sets basic authorization credentials

        // Remove null values
        $indexWoNull = array_values(array_filter($index, function ($value) {
            return $value !== "";
        }));

        $query = [
            // 'size' => 3,
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'valid_until' => [
                                    'lte' => 'now+90d'
                                ]
                            ]
                        ], [
                            "match" => [
                                "departments.id" => $deptId
                            ]
                        ],
                        [
                            "terms" => [
                                "_index" => $indexWoNull
                            ]
                        ]
                    ]
                ]
            ],
            "sort" => [
                [
                    "valid_until" => "asc"
                ],
                [
                    "created_at" => "asc"
                ]
            ]
        ];

        if ($searchAfter != null) {
            $query['search_after'] = $searchAfter;
        }

        $jsonData = json_encode($query);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        if ($response === false) {
            // Handle the error
            $error = curl_error($ch);
            curl_close($ch);
            die("cURL request failed: $error");
        }

        // Handle the successful response
        curl_close($ch);
        $result = json_decode($response, true);

        // Process $result as needed
        if (isset($result['hits']['hits'])) {
            $data = $result['hits']['hits'];
        } else {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'data' => $data,
                'total' => $result['hits']['total']['value'],
                'seachAfter' => $searchAfter
            ]));
    }

    public function search()
    {
        $keyword = $this->input->post('keyword');
        $searchAfter = $this->input->post('search_after');
        $deptId = $this->input->post('department_id');
        $index = $this->input->post('index');
        $selectedAggs = $this->input->post('aggs') != null ? $this->input->post('aggs') : [];

        $url = config_item('base_es_url').'/_search'; // Replace with your Elasticsearch endpoint and index
       $username = config_item('es_username');
        $password= config_item('es_password');
        $certificatePath = ROOT_PATH . '/ca.crt';
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Disables SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Disables SSL host verification
        curl_setopt($ch, CURLOPT_CAINFO, $certificatePath); // Set path to the self-signed certificate
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Sets basic authorization credentials

        // Remove null values
        $indexWoNull = array_values(array_filter($index, function ($value) {
            return $value !== "";
        }));

        $query = [
            // 'size' => 3,
            "query" => [
                "bool" => [
                    "must" => [
                        [
                            "bool" => [
                                "should" => [
                                    [
                                        "match" => [
                                            "doc_code" => [
                                                "query" => $keyword,
                                                // "fuzziness" => 'AUTO',
                                                // "operator"=> "and"
                                            ]

                                        ]
                                    ],
                                    [
                                        "match" => [
                                            "doc_name" => [
                                                "query" => $keyword,
                                                // "fuzziness" => 'AUTO',
                                                // "operator"=> "and"
                                            ]

                                        ]
                                    ]
                                ]
                            ]
                        ], [
                            "match" => [
                                "departments.id" => $deptId
                            ]
                        ]/*,
                        [
                            "terms" => [
                                "_index" => $indexWoNull
                            ]
                        ]*/
                    ]
                ]
            ],
            "sort" => [
                [
                    "_score" => "desc"
                ],
                [
                    "created_at" => "asc"
                ]
            ],
            "aggs" => [
                "types" => [
                    "terms" => [
                        "field" => "_index"
                    ]
                ]
            ]
        ];

        if ($searchAfter != null) {
            $query['search_after'] = $searchAfter;
        }

        if(!empty($selectedAggs)){
            array_push( $query['query']['bool']['must'],[
                "terms" => [
                    "_index" => $selectedAggs
                ]
                ]);
        //    var_dump($query);exit;
        }else{
            array_push( $query['query']['bool']['must'],[
                "terms" => [
                    "_index" => $indexWoNull
                ]
                ]);
        }

        $jsonData = json_encode($query);
        $t1 =$query;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        if ($response === false) {
            // Handle the error
            $error = curl_error($ch);
            curl_close($ch);
            die("cURL request failed: $error");
        }


        //query 2
        $query = [
            'size' => 0,
            "query" => [
                "bool" => [
                    "must" => [
                        [
                            "bool" => [
                                "should" => [
                                    [
                                        "match" => [
                                            "doc_code" => [
                                                "query" => $keyword,
                                                // "fuzziness" => 'AUTO',
                                                // "operator"=> "and"
                                            ]

                                        ]
                                    ],
                                    [
                                        "match" => [
                                            "doc_name" => [
                                                "query" => $keyword,
                                                // "fuzziness" => 'AUTO',
                                                // "operator"=> "and"
                                            ]

                                        ]
                                    ]
                                ]
                            ]
                        ], [
                            "match" => [
                                "departments.id" => $deptId
                            ]
                        ],
                        [
                            "terms" => [
                                "_index" => $indexWoNull
                            ]
                        ]
                    ]
                ]
            ],
            "sort" => [
                [
                    "_score" => "desc"
                ],
                [
                    "created_at" => "asc"
                ]
            ],
            "aggs" => [
                "types" => [
                    "terms" => [
                        "field" => "_index"
                    ]
                ]
            ]
        ];

        if ($searchAfter != null) {
            $query['search_after'] = $searchAfter;
        }

        $jsonData = json_encode($query);

        $t2 =$query;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response2 = curl_exec($ch);
        if ($response2 === false) {
            // Handle the error
            $error = curl_error($ch);
            curl_close($ch);
            die("cURL request failed: $error");
        }
        $result2 = json_decode($response2, true);
        $aggs = $result2['aggregations']['types']['buckets'];

        // Handle the successful response
        curl_close($ch);
        $result = json_decode($response, true);

        // Process $result as needed
        if (isset($result['hits']['hits'])) {
            // if ($selectedAggs != null) {
            //     $data = array_values(array_filter($result['hits']['hits'], function ($item) use ($selectedAggs) {
            //         return in_array($item['_index'], $selectedAggs);
            //     }));
            // } else {
            //     $data = $result['hits']['hits'];
            // }
            $data = $result['hits']['hits'];
            // $aggs = $result['aggregations']['types']['buckets'];
        } else {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'data' => $data,
                'aggs' => $aggs,
                'total' => $result['hits']['total']['value'],
                'selected_aggs' => $selectedAggs,
                'keyword' => $keyword
                ,'t1'=>$t1
                ,'t2'=>$t2
            ]));
    }
}
