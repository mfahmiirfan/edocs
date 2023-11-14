<?php
defined('BASEPATH') or exit('No direct script access allowed');

use NcJoes\OfficeConverter\OfficeConverter;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FormController extends CI_Controller
{
    public $is_token_verify_hookable = TRUE;
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Form', 'form');
        $this->load->model('Department', 'department');

        $this->load->helper('cookie');
        $this->load->helper('download');
        $this->load->helper('url');
        $this->load->helper('encryption_helper');
    }

    public function index()
    {
        $params = $this->input->get();

        $data = $this->form->findAll($params);
        if (!$data) {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function getPaginated()
    {
        $params = $this->input->get();

        $data = $this->form->findAllPaginated($params);
        if (!$data) {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function show($encrypted)
    {
        $id = decryptID($encrypted);
        $data = $this->form->find($id);
        if (!$data) {
            $data = (object)[];
        }
        $data['sli_edocs_form_id'] = encryptID($data['sli_edocs_form_id']);
        $data['departments'] = $this->form->getDepartmentsByDocId($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function store()
    {
        $data = $this->input->post();

        $config['upload_path']          = './uploads/docs/form/';
        $config['allowed_types']        = 'pdf|xls|xlsx|doc|docx|ppt|pptx';
        // $config['max_size']             = 5000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        $config['file_name'] = time() . "_" . preg_replace('/[^A-Za-z0-9.]/', "", $_FILES['file']['name']);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file')) {
            // echo json_encode(array('error' => $this->upload->display_errors()));exit;
        } else {
            $uploaded = $this->upload->data();

            switch ($uploaded['file_ext']) {
                case ".doc":
                case ".docx":
                    $converter = new OfficeConverter($uploaded['full_path'], null, 'soffice', false);
                    $converter->convertTo("$uploaded[raw_name].pdf");
                    break;
                case ".ppt":
                case ".pptx":
                    $converter = new OfficeConverter($uploaded['full_path'], null, 'simpress', false);
                    $converter->convertTo("$uploaded[raw_name].pdf");
                    break;
                case ".xls":
                case ".xlsx":
                    $this->convertToXLS($uploaded['full_path'], "$uploaded[raw_name].pdf");
                    break;
            }

            $data['sli_edocs_form_file'] = "$config[file_name]";
        }

        if ($this->form->save($data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'Document stored successfully'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'message' => 'Document store failed.'
                ]));
        }
    }

    public function update($encrypted)
    {
        $id = decryptID($encrypted);

        $data = $this->input->post();

        $config['upload_path']          = './uploads/docs/form/';
        $config['allowed_types']        = 'pdf|xls|xlsx|doc|docx|ppt|pptx';
        // $config['max_size']             = 5000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if (!empty($_FILES['file']['name'])) {
            $idCard = $this->form->find($id);
            if (isset($idCard['sli_edocs_form_file'])) {
                unlink("./uploads/docs/form/$idCard[sli_edocs_form_file]");

                $segs = explode('.', $idCard['sli_edocs_form_file']);
                if (file_exists("./uploads/docs/form/$segs[0].pdf")) {
                    unlink("./uploads/docs/form/$segs[0].pdf");
                }
            }

            $config['file_name'] = time() . "_" . preg_replace('/[^A-Za-z0-9.]/', "", $_FILES['file']['name']);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                // echo json_encode(array('error' => $this->upload->display_errors()));exit;
            } else {
                $uploaded = $this->upload->data();

                switch ($uploaded['file_ext']) {
                    case ".doc":
                    case ".docx":
                        $converter = new OfficeConverter($uploaded['full_path'], null, 'soffice', false);
                        $converter->convertTo("$uploaded[raw_name].pdf");
                        break;
                    case ".ppt":
                    case ".pptx":
                        $converter = new OfficeConverter($uploaded['full_path'], null, 'simpress', false);
                        $converter->convertTo("$uploaded[raw_name].pdf");
                        break;
                    case ".xls":
                    case ".xlsx":
                        $this->convertToXLS($uploaded['full_path'], "$uploaded[raw_name].pdf");
                        break;
                }

                $data['sli_edocs_form_file'] = "$config[file_name]";
            }
        }

        if ($this->form->update($id, $data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'Document updated successfully'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'message' => 'Document update failed.'
                ]));
        }
    }

    public function delete($encrypted)
    {
        $id = decryptID($encrypted);
        $data = $this->input->get();

        $doc = $this->form->find($id);
        if (isset($doc['sli_edocs_form_file'])) {
            unlink("./uploads/docs/form/$doc[sli_edocs_form_file]");

            $segs = explode('.', $doc['sli_edocs_form_file']);
            if (file_exists("./uploads/docs/form/$segs[0].pdf")) {
                unlink("./uploads/docs/form/$segs[0].pdf");
            }
        }
        if (!$this->form->deleteDepartmentsByDocId($id)) {
            return;
        }
        if ($this->form->destroy($id, $data['user_id'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'Document deleted successfully'
                ]));
        }
    }

    public function download($encrypted)
    {
        $id = decryptID($encrypted);

        $postData = $this->input->post();

        $data = $this->form->find($id);

        if (!$data) {
            $data = (object)[];
        }
        //save log
        $this->form->saveLog($id, $postData['user_id'], 'DNLOAD');
        force_download("./uploads/docs/form/$data[sli_edocs_form_file]", NULL);
    }

    public function preview($encrypted)
    {
        $id = decryptID($encrypted);
        $postData = $this->input->post();

        $data = $this->form->find($id);
        if (!$data) {
            show_404();
        }
        $fileName = $data['sli_edocs_form_file'];
        $filePath = "./uploads/docs/form/$data[sli_edocs_form_file]";

        $segments = explode('.', $fileName);
        $extension = $segments[count($segments) - 1];
        $preview = str_replace(".$extension", "", $fileName);

        if (!file_exists("./uploads/docs/form/$preview.pdf")) {
            switch (".$extension") {
                case ".doc":
                case ".docx":
                    $converter = new OfficeConverter($filePath, null, 'soffice', false);
                    $converter->convertTo("$preview.pdf");
                    break;
                case ".ppt":
                case ".pptx":
                    $converter = new OfficeConverter($filePath, null, 'simpress', false);
                    $converter->convertTo("$preview.pdf");
                    break;
                case ".xls":
                case ".xlsx":
                    $this->convertToXLS($filePath, "$preview.pdf");
                    break;
            }
        }

        $uri = base_url() . "backend/uploads/docs/form/$preview.pdf";
        $this->load->view('document_preview', [
            'uri' => $uri,
            'index' => 'form',
            'doc_code' => $data['sli_edocs_form_code'],
            'doc_name' => $data['sli_edocs_form_name'],
            'doc_file' => $data['sli_edocs_form_file'],
            'valid_until' => $data['sli_edocs_form_valid_until_date'] != '9999-12-31' ? $data['sli_edocs_form_valid_until_date'] : '-'
        ]);
        //save log
        $this->form->saveLog($id, $postData['user_id'], 'VIEW');
    }

    public function export()
    {
        $postData = $this->input->post();

        $data = $this->form->findAllByFilter($postData);

        if (!$data) {
            $data = (object)[];
        }

        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

            return;
        }
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        // $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        //     ->setLastModifiedBy('Maarten Balliauw')
        //     ->setTitle('Office 2007 XLSX Test Document')
        //     ->setSubject('Office 2007 XLSX Test Document')
        //     ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
        //     ->setKeywords('office 2007 openxml php')
        //     ->setCategory('Test result file');

        // Add some data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Doc. Code')
            ->setCellValue('B1', 'Doc. Name')
            ->setCellValue('C1', 'Doc. Filename')
            ->setCellValue('D1', 'Doc. Created Date')
            ->setCellValue('E1', 'Doc. Expired Date');

        //fill color skublue
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('bbdefb');


        $i = 2;
        foreach ($data as $row) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('A' . $i, $row['sli_edocs_form_code'])
                ->setCellValue('B' . $i, $row['sli_edocs_form_name'])
                ->setCellValue('C' . $i, $row['sli_edocs_form_file'])
                ->setCellValue('D' . $i, $row['sli_edocs_form_created_date'])
                ->setCellValue('E' . $i, $row['sli_edocs_form_valid_until_date']);
            $i++;
        }

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

        // Miscellaneous glyphs, UTF-8
        // $spreadsheet->setActiveSheetIndex(0)
        //     ->setCellValue('A4', 'Miscellaneous glyphs')
        //     ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

        // Rename worksheet
        // $spreadsheet->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="01simple.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'op' => 'ok',
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData),
            // 'image' => FCPATH . 'public/uploads/images/' . $newName, 'contoh' => __DIR__ . '/resources/logo_ubuntu_transparent.png', '__DIR__' => __DIR__
        );

        die(json_encode($response));
    }

    function convertToXLS($inputFileName, $desiredFileName)
    {
        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            $worksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
        $writer->setPreCalculateFormulas(false);
        $writer->save("./uploads/docs/form/" . $desiredFileName);

        // ob_start();
        // $writer->save('php://output');
        // $xlsData = ob_get_contents();
        // ob_end_clean();

        // return "data:application/pdf;base64," . base64_encode($xlsData);
    }

    public function initiateIndex()
    {
        if ($this->form->initiate()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'Document stored successfully'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'message' => 'Document store failed.'
                ]));
        }
    }
}
