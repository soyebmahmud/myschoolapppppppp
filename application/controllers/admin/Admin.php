<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("classteacher_model");
        $this->load->model("Staff_model");
        $this->load->library('Enc_lib');
		$this->sch_setting_detail = $this->setting_model->getSetting();
		$this->load->library('SaasValidation');
		$this->load->library('ResourceQuota');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function updateAddonVerify()
    {
        $this->form_validation->set_rules('addon', 'Addon', 'required|trim|xss_clean');
        $this->form_validation->set_rules('addon_check_update_envato_market_purchase_code', 'Purchase Code', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'addon'                       => form_error('addon'),
                'addon_check_update_envato_market_purchase_code' => form_error('addon_check_update_envato_market_purchase_code'),
            );
            $array = array('status' => '0', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            $response = $this->auth->addon_update_check();
        }
    }

    public function dashboard()
    {
        $storageused = $this->saasvalidation->applicationQuotas();

        $data['storageused'] = [];

        if (
            is_array($storageused) &&
            isset($storageused['response']['data']) &&
            is_array($storageused['response']['data'])
        ) {
            $data['storageused'] = $storageused['response']['data'];
        }

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b) = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');

        $last_day_this_month  = date($Current_year.'-m-t');
        $total_students_heads = 0;

        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        $student_transport_fee = $this->studenttransportfee_model->getTransportDepositAmountBetweenDate($year_str_month, $year_end_month);
        $first_day_this_month = date('Y-m-01');
        $month_collection     = $this->whatever($getDepositeAmount, $first_day_this_month, $current_date);
        $month_transport_collection = $this->whatever($student_transport_fee, $first_day_this_month, $current_date);
        $data['month_collection'] = $month_collection+$month_transport_collection;
        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }
        $data['total_students'] = $total_students;
        $tot_roles = $this->role_model->get();
        foreach ($tot_roles as $key => $value) {
            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);
        }
        $data["roles"] = $count_roles;

        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = $this->lang->line(strtolower(date('F', $start_month)));
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            $tranport_amt  = $this->whatever($student_transport_fee, $month_start, $month_end);
            if (!IsNullOrEmptyString($return) || !IsNullOrEmptyString($tranport_amt)) {
                $s[] = convertBaseAmountCurrencyFormat($return+$tranport_amt);
            } else {
                $s[] = "0.00";
            }
            $start_month = strtotime("+1 month", $start_month);
        }

        $ex = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {
            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);
            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);
            if (!empty($expense_monthly)) {
                $amt = 0;
                $ex[] = two_digit_float($amt + convertBaseAmountCurrencyFormat($expense_monthly->amount));
            }
            $start_session_month = strtotime("+1 month", $start_session_month);
        }
        $data['yearly_collection'] = $s;
        $data['yearly_expense'] = $ex;
        $data['total_month'] = $total_month;

        $startdate = date('m/01/Y');
        $enddate   = date('m/t/Y');
        $start     = strtotime($startdate);
        $end       = strtotime($enddate);
        $currentdate = $start;
        $month_days = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date = date('Y-m-d', $currentdate);
            $month_days[] = date('d', $currentdate);
            $coll_amt = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $tranport_amt = $this->whatever($student_transport_fee, $cur_date, $cur_date);
            $days_collection[] = convertBaseAmountCurrencyFormat($coll_amt+$tranport_amt);
            $currentdate = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection'] = $days_collection;

        $startdate = date('m/01/Y');
        $enddate = date('m/t/Y');
        $start = strtotime($startdate);
        $end = strtotime($enddate);
        $currentdate = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date = date('Y-m-d', $currentdate);
            $month_days[] = date('d', $currentdate);
            $currentdate = strtotime('+1 day', $currentdate);
            $ct = $this->getExpensebyday($cur_date);
            $days_expense[] = convertBaseAmountCurrencyFormat($ct);
        }
        $data['days_expense'] = $days_expense;
        $student_fee_history = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata = $this->customlib->getUserData();
        $data["role"] = $userdata["user_type"];
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $student_due_fee = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);
        $student_transport_fee = $this->studentfeemaster_model->getTransportFeesByDueDate($start_date, $end_date);
        $data['fees_awaiting'] = $student_due_fee;

        $total_fess=0; $total_paid=0; $total_unpaid=0; $total_partial=0;
        if (!empty($student_transport_fee)) {
            foreach ($student_transport_fee as $transport_fees_key => $transport_fees_value) {
                $amount_to_be_taken = 0;
                if ($transport_fees_value->fees > 0) $amount_to_be_taken = $transport_fees_value->fees;
                if ($amount_to_be_taken > 0) {
                    $total_fess++;
                    if (is_string($transport_fees_value->amount_detail) && is_array(json_decode($transport_fees_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = json_decode($transport_fees_value->amount_detail);
                        $amt_=0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) $total_paid++; elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) $total_partial++;
                    } else $total_unpaid++;
                }
            }
        }
        if (!empty($data['fees_awaiting'])) {
            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {
                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) { if ($awaiting_value->amount > 0) $amount_to_be_taken = $awaiting_value->amount; }
                elseif ($awaiting_value->is_system == 0) { if ($awaiting_value->fee_amount > 0) $amount_to_be_taken = $awaiting_value->fee_amount; }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;
                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = json_decode($awaiting_value->amount_detail);
                        $amt_=0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) $total_paid++; elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) $total_partial++;
                    } else $total_unpaid++;
                }
            }
        }

        $incomegraph = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        foreach ($incomegraph as $key => $value) $incomegraph[$key]['total'] = convertBaseAmountCurrencyFormat($value['total']);
        $data['incomegraph'] = $incomegraph;
        $expensegraph = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        foreach ($expensegraph as $key => $value) {
            $expensegraph[$key]['total'] = convertBaseAmountCurrencyFormat($value['total']);
            if (!empty($value['total'])) $month_expense += convertBaseAmountCurrencyFormat($value['total']);
        }
        $data['expensegraph'] = $expensegraph;
        $data['month_expense'] = $month_expense;
        $enquiry = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;
        $data['fees_overview'] = array(
            'total_unpaid' => $total_unpaid,
            'unpaid_progress' => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid' => $total_paid,
            'paid_progress' => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial' => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );
        $data['total_counter'] = $total_counter;
        $data['enquiry'] = $enquiry;
        $data['total_teachers'] = $this->staff_model->getStaffCount();
        $data['total_staff'] = $this->staff_model->getTotalStaff();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }

    public function updateSession()
    {
        $session = $this->input->post('popup_session');
        $session_array = $this->session->has_userdata('session_array');
        if ($session_array) $this->session->unset_userdata('session_array');
        $session = $this->session_model->get($session);
        $session_array = array('session_id' => $session['id'], 'session' => $session['session']);
        $this->session->set_userdata('session_array', $session_array);
        echo json_encode(array('status' => 1, 'message' => $this->lang->line('session_changed_successfully')));
    }

    public function backup()
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_view')) access_denied();
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/backup');
        $this->session->set_userdata('inner_menu', 'admin/backup');
        $data['title'] = $this->lang->line('backup_history');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            if ($this->input->post('backup') == "upload") {
                $file = $_FILES['db_file']['tmp_name'];
                if ($file) { $this->load->library('backup'); $this->backup->import_database($file); }
            }
        }
        $data['list'] = $this->backup_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/backup/index', $data);
        $this->load->view('layout/footer', $data);
    }
}
