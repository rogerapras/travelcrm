<?php
/**
 * TravelCRM
 *
 * An open source CRM system for travel agencies
 *
 * @author		Mazvv (vitalii.mazur@gmail.com)
 * @license		GNU GPLv3 (http://gplv3.fsf.org) 
 * @link		http://www.travelcrm.org.ua
 */
include_once APPPATH."libraries/core/Crmcontroller.php";
class Aircompanies extends Crmcontroller {
	private $jtp = array('val'=>'rid', 'scr'=>'name', 'val_p'=>'_aircompanies_rid', 'scr_p'=>'aircompany_name');
	
	public function __construct(){
		parent::__construct();
		$this->lang->load('aircompanies');
		$this->load->model('aircompanies_model');
		
		# Overwrite jtp mapper if need 
		# It's very usable if form has some value_pickers with one type
		if(element('val_p', $this->a_uri_assoc, null)) $this->jtp['val_p'] = element('val_p', $this->a_uri_assoc, null);
		if(element('scr_p', $this->a_uri_assoc, null)) $this->jtp['scr_p'] = element('scr_p', $this->a_uri_assoc, null);
	}

	public function _remap($m_Name){
		switch ($m_Name) {
			case 'create': {$this->create();break;}
			case 'edit': {$this->edit();break;}
			case 'details': {$this->details();break;}
			case 'remove': {$this->remove();break;}
			case 'move': {$this->move();break;}
			case 'sort': {$this->sort();break;}
			case 'vcreate': {$this->vcreate();break;}
			case 'vedit': {$this->vedit();break;}
			case 'vdetails': {$this->vdetails();break;}
			case 'vremove': {$this->vremove();break;}
			case 'vmove': {$this->vmove();break;}
			case 'vsort': {$this->vsort();break;}
			case 'vjournal': 
			case 'vfind': {$this->vjournal(); break;}
			case 'limit': {$this->limit();break;}
			case 'help': {$this->help(); break;}
			default: $this->index();
		}
	}

	public function journal(){
		$data = array();
		$data['title'] = lang('AIRCOMPANIES_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->find();
		$data['fields']['rid'] = array('label'=>'Id', 'colwidth'=>'5%', 'sort'=>True); 
		$data['fields']['code'] =  array('label'=>lang('CODE'), 'colwidth'=>'15%', 'sort'=>True);
		$data['fields']['iata'] =  array('label'=>lang('IATA'), 'colwidth'=>'15%', 'sort'=>True);
		$data['fields']['name'] =  array('label'=>lang('NAME'), 'colwidth'=>'40%', 'sort'=>True); 
		$data['fields']['archive'] = array('label'=>lang('ARCHIVE'), 'colwidth'=>'10%', 'sort'=>True, 'type'=>'yes_no'); 
		$data['fields']['modifyDT'] = array('label'=>lang('MODIFYDT'), 'colwidth'=>'20%', 'sort'=>True); 
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->aircompanies_model->get_ds();
		$data['paging'] = $this->get_paging($this->aircompanies_model->get_calc_rows());
		return $this->load->view('standart/grid', $data, True);		
	}

	private function create(){
		$data = array();
		$this->set_validation();
		$data['title'] = lang('AIRCOMPANIES_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		if ($this->form_validation->run() === True){
			if($rid = $this->aircompanies_model->create_record()){
				$this->session->set_flashdata('success', True);
				redirect(get_currcontroller()."/edit/$rid", 'refresh');
				return;
			}
			else {
				$data['success'] = false;
			} 
		}
		$data['content'] = $this->load->view('aircompanies/create', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}

	
	private function edit(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$this->set_validation();
		$data['title'] = lang('AIRCOMPANIES_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->aircompanies_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		if(!$data['ds']) show_404(); 
		if ($this->form_validation->run() === True){
			if($this->aircompanies_model->update_record()) $data['success'] = true;
			else $data['success'] = false;
			$data['ds'] = $this->aircompanies_model->get_edit($rid);
		}
		$data['content'] = $this->load->view('aircompanies/edit', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}

	
	private function details(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$data['title'] = lang('AIRCOMPANIES_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->aircompanies_model->get_edit($rid);
		if(!$data['ds']) show_404(); 
		$data['content'] = $this->load->view('aircompanies/details', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}


	private function find(){
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('code', lang('CODE'), 'trim');
		$this->form_validation->set_rules('name', lang('NAME'), 'trim');
		$this->form_validation->set_rules('iata', lang('IATA'), 'trim');
		$this->form_validation->set_rules('archive', lang('HIDE_ARCHIVE'), 'trim');
		if ($this->form_validation->run() == True){
			$search_rule = array();
			if($this->input->post('code')) $search_rule['like']['_aircompanies.code'] = $this->input->post('code');
			if($this->input->post('name')) $search_rule['like']['_aircompanies.name'] = $this->input->post('name');
			if($this->input->post('iata')) $search_rule['like']['_aircompanies.iata'] = $this->input->post('iata');
			if($this->input->post('archive')==0) $search_rule['where']['_aircompanies.archive'] = $this->input->post('archive');
			$this->set_searchrule($search_rule);
		}
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		return $this->load->view('aircompanies/find', $data, True);
	}


	private function move(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('AIRCOMPANIES_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->aircompanies_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		if(!$data['ds']) show_404(); 
		if ($this->form_validation->run() === True){
			if($this->aircompanies_model->move_record()) $data['success'] = true;
			else $data['success'] = false;
			$data['ds'] = $this->aircompanies_model->get_edit($rid);
		}
		$data['content'] = $this->load->view('standart/move', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	
	public function check_unique_name($code){
		$rid = $this->input->post('rid'); # для случая если проверка идет при редактировании
		if($this->aircompanies_model->check_unique($code, 'name', $rid)){
			$this->form_validation->set_message('check_unique_name', lang('aircompanies_NAME_NOTUNIQUE'));
			return False;
		}
		return True;
	}

	
	public function check_unique_name_lat($code){
		$rid = $this->input->post('rid'); # для случая если проверка идет при редактировании
		if($this->aircompanies_model->check_unique($code, 'name_lat', $rid)){
			$this->form_validation->set_message('check_unique_name_lat', lang('AIRCOMPANIES_NAME_LAT_NOTUNIQUE'));
			return False;
		}
		return True;
	}


	/* Операции для Value Picker */
	private function vcreate(){
		$data = array();
		$this->set_validation();
		$data['title'] = lang('AIRCOMPANIES_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		if ($this->form_validation->run() === True){
			if($rid = $this->aircompanies_model->create_record()){
				$this->session->set_flashdata('success', True);
				redirect(get_currcontroller()."/vedit/$rid", 'refresh');
				return;
			}
			else {
				$data['success'] = false;
			} 
		}
		$data['content'] = $this->load->view('aircompanies/vcreate', $data, True);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	
	public function vjournal(){
		$data = array();
		$data['title'] = lang('aircompanies_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->vfind();
		$data['fields']['rid'] = array('label'=>'Id', 'colwidth'=>'5%', 'sort'=>True);
		$data['fields']['code'] =  array('label'=>lang('CODE'), 'colwidth'=>'15%', 'sort'=>True);
		$data['fields']['iata'] =  array('label'=>lang('IATA'), 'colwidth'=>'15%', 'sort'=>True);
		$data['fields']['name'] =  array('label'=>lang('NAME'), 'colwidth'=>'40%', 'sort'=>True); 
		$data['fields']['archive'] = array('label'=>lang('ARCHIVE'), 'colwidth'=>'10%', 'sort'=>True, 'type'=>'yes_no'); 
		$data['jtp'] = $this->jtp;
		#$data['fields']['modifyDT'] = array('label'=>lang('MODIFYDT'), 'colwidth'=>'20%', 'sort'=>True); 
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->aircompanies_model->get_ds();
		$data['paging'] = $this->get_paging($this->aircompanies_model->get_calc_rows(), True);
		$content = $this->load->view('standart/vgrid', $data, True);
		$this->load->view('layouts/valuepicker_layout', array('content'=>$content));		
		return;
	}
	
	private function vedit(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$this->set_validation();
		$data['title'] = lang('AIRCOMPANIES_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['jtp'] = $this->jtp;
		$data['ds'] = $this->aircompanies_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		if(!$data['ds']) show_404(); 
		if ($this->form_validation->run() === True){
			if($this->aircompanies_model->update_record()) $data['success'] = true;
			else $data['success'] = false;
			$data['ds'] = $this->aircompanies_model->get_edit($rid);
		}
		$data['content'] = $this->load->view('aircompanies/vedit', $data, True);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	

	private function vdetails(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$data['title'] = lang('AIRCOMPANIES_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['jtp'] = $this->jtp;
		$data['ds'] = $this->aircompanies_model->get_edit($rid);
		if(!$data['ds']) show_404(); 
		$data['content'] = $this->load->view('aircompanies/vdetails', $data, True);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}



	private function vfind(){
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('code', lang('CODE'), 'trim');
		$this->form_validation->set_rules('name', lang('NAME'), 'trim');
		$this->form_validation->set_rules('iata', lang('IATA'), 'trim');
		$this->form_validation->set_rules('archive', lang('HIDE_ARCHIVE'), 'trim');
		if ($this->form_validation->run() == True){
			$search_rule = array();
			if($this->input->post('code')) $search_rule['like']['_aircompanies.code'] = $this->input->post('code');
			if($this->input->post('name')) $search_rule['like']['_aircompanies.name'] = $this->input->post('name');
			if($this->input->post('iata')) $search_rule['like']['_aircompanies.iata'] = $this->input->post('iata');
			if($this->input->post('archive')==0) $search_rule['where']['_aircompanies.archive'] = $this->input->post('archive');
			$this->set_searchrule($search_rule);
		}
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		return $this->load->view('aircompanies/vfind', $data, True);
	}
	
	private function vmove(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('AIRCOMPANIES_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->aircompanies_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		if(!$data['ds']) show_404(); 
		if ($this->form_validation->run() === True){
			if($this->aircompanies_model->move_record()) $data['success'] = true;
			else $data['success'] = false;
			$data['ds'] = $this->aircompanies_model->get_edit($rid);
		}
		$data['content'] = $this->load->view('standart/vmove', $data, True);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}
	
	private function set_validation(){
		$this->form_validation->set_rules('code', lang('CODE'), 'required|exact_length[3]|numeric|callback_check_unique_code');
		$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
		$this->form_validation->set_rules('iata', lang('IATA'), 'required|exact_length[2]|alpha_numeric|trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		return;		
	}
}

