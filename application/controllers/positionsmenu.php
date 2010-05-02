<?php/** * TravelCRM * * An open source CRM system for travel agencies * * @author		Mazvv (vitalii.mazur@gmail.com) * @license		GNU GPLv3 (http://gplv3.fsf.org)  * @link		http://www.travelcrm.org.ua */include_once APPPATH."libraries/core/Crmcontroller.php";class Positionsmenu extends Crmcontroller {	public function __construct(){		parent::__construct();		$this->lang->load('positionsmenu');		$this->load->model('positionsmenu_model');		$this->load->helper('positions');		$this->load->helper('positionsmenus');		$this->load->helper('modules');	}	public function _remap($m_Name){		switch ($m_Name) {			case 'create': {$this->create();break;}			case 'edit': {$this->edit();break;}			case 'details': {$this->details();break;}			case 'remove': {$this->remove();break;}			case 'move': {$this->move();break;}			case 'sort': {$this->sort();break;}			case 'mlist': {$this->output->set_output($this->mlist());break;}			case 'limit': {$this->limit();break;}			case 'help': {$this->help(); break;}			default: $this->index();		}	}	public function journal(){		$data = array();		$data['title'] = lang('POSITIONSMENU_TITLE');		$data['orid'] = $this->get_orid();		$data['sort'] = $this->get_session('sort');		$data['find'] = $this->find();		$data['fields']['rid'] = array('label'=>'Id', 'colwidth'=>'5%', 'sort'=>True); 		$data['fields']['position_name'] =  array('label'=>lang('POSITION'), 'colwidth'=>'20%', 'sort'=>True);		$data['fields']['item_name'] =  array('label'=>lang('ITEM_NAME'), 'colwidth'=>'20%', 'sort'=>True);		$data['fields']['module_controller'] =  array('label'=>lang('CONTROLLER'), 'colwidth'=>'20%', 'sort'=>True);		$data['fields']['archive'] = array('label'=>lang('ARCHIVE'), 'colwidth'=>'10%', 'sort'=>True, 'type'=>'yes_no'); 		$data['fields']['modifyDT'] = array('label'=>lang('MODIFYDT'), 'colwidth'=>'20%', 'sort'=>True); 		$data['tools'] = $this->get_tools(); 		$data['ds'] = $this->positionsmenu_model->get_ds();		$data['paging'] = $this->get_paging($this->positionsmenu_model->get_calc_rows());		return $this->load->view('standart/grid', $data, True);			}	private function create(){		$data = array();		$this->set_validation();		$data['title'] = lang('POSITIONSMENU_TITLE_CREATE');		$data['orid'] = $this->get_orid();		$data['success'] = null;		if ($this->form_validation->run() === True){			if($rid = $this->positionsmenu_model->create_record()){				$this->session->set_flashdata('success', True);				redirect(get_currcontroller()."/edit/$rid", 'refresh');				return;			}			else {				$data['success'] = false;			} 		}		$data['content'] = $this->load->view('positionsmenu/create', $data, True);		return $this->load->view('layouts/main_layout', $data);	}		private function edit(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$this->set_validation();		$data['title'] = lang('POSITIONSMENU_TITLE_EDIT');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->positionsmenu_model->get_edit($rid);		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;		if(!$data['ds']) show_404(); 		if ($this->form_validation->run() === True){			if($this->positionsmenu_model->update_record()) $data['success'] = true;			else $data['success'] = false;			$data['ds'] = $this->positionsmenu_model->get_edit($rid);		}		$data['content'] = $this->load->view('positionsmenu/edit', $data, True);		return $this->load->view('layouts/main_layout', $data);	}	private function details(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$data['title'] = lang('POSITIONSMENU_TITLE_DETAILS');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->positionsmenu_model->get_edit($rid);		if(!$data['ds']) show_404(); 		$data['content'] = $this->load->view('positionsmenu/details', $data, True);		return $this->load->view('layouts/main_layout', $data);	}	private function find(){		$data['orid'] = $this->get_orid();		$this->form_validation->set_rules('item_name', lang('ITEM_NAME'), 'trim');		$this->form_validation->set_rules('_positions_rid', lang('POSITION'), 'trim');		if ($this->form_validation->run() == True){			$search_rule = array();			if($this->input->post('item_name')) $search_rule['_positions_menus.item_name'] = $this->input->post('item_name');			if($this->input->post('_positions_rid')) $search_rule['_positions.rid'] = $this->input->post('_positions_rid');			$this->set_searchrule($search_rule);		}		$data['search'] = $this->get_session('searchrule');		return $this->load->view('positionsmenu/find', $data, True);	}		private function mlist(){		$prid = $this->input->post('prid');		if(!$prid) return null;		return build_tree_dropdown($prid);	}		private function move(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');		$data['title'] = lang('POSITIONSMENU_TITLE_MOVE');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->positionsmenu_model->get_edit($rid);		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;		if(!$data['ds']) show_404(); 		if ($this->form_validation->run() === True){			if($this->positionsmenu_model->move_record()) $data['success'] = true;			else $data['success'] = false;			$data['ds'] = $this->positionsmenu_model->get_edit($rid);		}		$data['content'] = $this->load->view('standart/move', $data, True);		return $this->load->view('layouts/main_layout', $data);	}	public function check_unique_module($_modules_rid){		if(!$_modules_rid) return True;		$rid = $this->input->post('rid'); # для случая если проверка идет при редактировании		if($this->positionsmenu_model->check_unique_module($_modules_rid, $this->input->post('_positions_rid'), $rid)){			$this->form_validation->set_message('check_unique_module', lang('POSITIONSMENU_MODULE_NOTUNIQUE'));			return False;		}		return True;	}		private function set_validation(){		$this->form_validation->set_rules('_positions_rid', lang('POSITION'), 'required|trim');		$this->form_validation->set_rules('item_name', lang('ITEM_NAME'), 'trim|required');		$this->form_validation->set_rules('_modules_rid', lang('MODULE'), 'trim|callback_check_unique_module');		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');		return;			}}?>