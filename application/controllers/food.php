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
class Food extends Crmcontroller {
	
	public function __construct(){
		parent::__construct();
		$this->lang->load('food');
		$this->load->model('food_model');
	}
	
	public function _remap($m_Name){
		switch ($m_Name) {
			case 'create': {$this->create();break;}
			case 'edit': {$this->edit();break;}
			case 'details': {$this->details();break;}
			case 'remove': {$this->remove();break;}
			case 'move': {$this->move();break;}
			case 'sort': {$this->sort();break;}
			case 'limit': {$this->limit();break;}
			case 'help': {$this->help(); break;}
			default: $this->index();
		}
	}
	
	public function journal(){
		$data = array();
		$data['title'] = lang('FOOD_TITLE');
		
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->find();
		$data['fields']['rid'] = array('label'=>'Id', 'colwidth'=>'5%', 'sort'=>True); 
		$data['fields']['code'] =  array('label'=>lang('CODE'), 'colwidth'=>'30%', 'sort'=>True);
		$data['fields']['food_name'] =  array('label'=>lang('NAME'), 'colwidth'=>'30%', 'sort'=>True); 
		$data['fields']['archive'] = array('label'=>lang('ARCHIVE'), 'colwidth'=>'5%', 'sort'=>True, 'type'=>'yes_no'); 
		$data['fields']['modifyDT'] = array('label'=>lang('MODIFYDT'), 'colwidth'=>'30%', 'sort'=>True); 
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->food_model->get_ds();
		$data['paging'] = $this->get_paging($this->food_model->get_calc_rows());
		return $this->load->view('standart/grid', $data, True);		
	}
	
	private function create(){
		$data = array();

		$this->form_validation->set_rules('code', lang('CODE'), 'required|trim|callback_check_unique_code');
		$this->form_validation->set_rules('food_name', lang('NAME'), 'required|trim|callback_check_unique_name');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');

		$data['title'] = lang('FOOD_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		if ($this->form_validation->run() === True){
			if($rid = $this->food_model->create_record()){
				$this->session->set_flashdata('success', True);
				redirect(get_currcontroller()."/edit/$rid", 'refresh');
				return;
			}
			else {
				$data['success'] = false;
			} 
		}
		$data['content'] = $this->load->view('food/create', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function edit(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();

		$this->form_validation->set_rules('code', lang('CODE'), 'required|trim|callback_check_unique_code');
		$this->form_validation->set_rules('food_name', lang('NAME'), 'required|trim|callback_check_unique_name');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		
		$data['title'] = lang('FOOD_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->food_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		if(!$data['ds']) show_404(); 
		if ($this->form_validation->run() === True){
			if($this->food_model->update_record()) $data['success'] = true;
			else $data['success'] = false;
			$data['ds'] = $this->food_model->get_edit($rid);
		}
		$data['content'] = $this->load->view('food/edit', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function details(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();

		$data['title'] = lang('FOOD_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->food_model->get_edit($rid);
		if(!$data['ds']) show_404(); 
		$data['content'] = $this->load->view('food/details', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function find(){
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('code', lang('CODE'), 'trim');
		$this->form_validation->set_rules('food_name', lang('NAME'), 'trim');
		$this->form_validation->set_rules('archive', lang('HIDE_ARCHIVE'), 'trim');
		if ($this->form_validation->run() == True){
			$search_rule = array();
			if($this->input->post('code')) $search_rule['like']['_food.code'] = $this->input->post('code');
			if($this->input->post('food_name')) $search_rule['like']['_food.food_name'] = $this->input->post('food_name');
			if($this->input->post('archive')==0) $search_rule['where']['_food.archive'] = $this->input->post('archive');			
			$this->set_searchrule($search_rule);
		}
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		return $this->load->view('food/find', $data, True);
	}
	
	
	private function move(){
		$rid = (int)$this->uri->segment(3);
		if(!$rid) show_404();
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('FOOD_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->food_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		if(!$data['ds']) show_404(); 
		if ($this->form_validation->run() === True){
			if($this->food_model->move_record()) $data['success'] = true;
			else $data['success'] = false;
			$data['ds'] = $this->food_model->get_edit($rid);
		}
		$data['content'] = $this->load->view('standart/move', $data, True);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	public function check_unique_code($code){
		$rid = $this->input->post('rid'); # для случая если проверка идет при редактировании
		if($this->food_model->check_unique($code, 'code', $rid)){
			$this->form_validation->set_message('check_unique_code', lang('FOOD_CODE_NOTUNIQUE'));
			return False;
		}
		return True;
	}
	
	public function check_unique_name($code){
		$rid = $this->input->post('rid'); # для случая если проверка идет при редактировании
		if($this->food_model->check_unique($code, 'name', $rid)){
			$this->form_validation->set_message('check_unique_name', lang('FOOD_NAME_NOTUNIQUE'));
			return False;
		}
		return True;
	}
	
}

?>