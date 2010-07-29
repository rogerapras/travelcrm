<?php/** * TravelCRM * * An open source CRM system for travel agencies * * @author		Mazvv (vitalii.mazur@gmail.com) * @license		GNU GPLv3 (http://gplv3.fsf.org)  * @link		http://www.travelcrm.org.ua */include_once APPPATH."libraries/core/Crmcontroller.php";class Advertisescompanies extends Crmcontroller {	private $jtp = array('val'=>'rid', 'scr'=>'company_name', 'val_p'=>'_advertisescompanies_rid', 'scr_p'=>'company_name'); 		public function __construct(){		parent::__construct();		$this->lang->load('advertisescompanies');		$this->load->model('advertisescompanies_model');		$this->load->helper('advertisescompanies');				# Overwrite jtp mapper if need 		# It's very usable if form has some value_pickers with one type		if(element('val_p', $this->a_uri_assoc, null)) $this->jtp['val_p'] = element('val_p', $this->a_uri_assoc, null);		if(element('scr_p', $this->a_uri_assoc, null)) $this->jtp['scr_p'] = element('scr_p', $this->a_uri_assoc, null);			}	public function _remap($m_Name){		switch ($m_Name) {			case 'create': {$this->create();break;}			case 'edit': {$this->edit();break;}			case 'details': {$this->details();break;}			case 'remove': {$this->remove();break;}			case 'move': {$this->move();break;}			case 'sort': {$this->sort();break;}			case 'vcreate': {$this->vcreate();break;}			case 'vedit': {$this->vedit();break;}			case 'vdetails': {$this->vdetails();break;}			case 'vremove': {$this->vremove();break;}			case 'vmove': {$this->vmove();break;}			case 'vsort': {$this->vsort();break;}			case 'vjournal': 			case 'vfind': {$this->vjournal(); break;}			case 'limit': {$this->limit();break;}			default: $this->index();		}	}	public function journal(){		$data = array();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE');		$data['orid'] = $this->get_orid();		$data['sort'] = $this->get_session('sort');		$data['find'] = $this->find();		$data['fields']['rid'] = array('label'=>'Id', 'colwidth'=>'5%', 'sort'=>True); 		$data['fields']['company_name'] =  array('label'=>lang('COMPANY'), 'colwidth'=>'35%', 'sort'=>True);		$data['fields']['company_type'] =  array('label'=>lang('COMPANY_TYPE'), 'colwidth'=>'35%', 'sort'=>True);		$data['fields']['archive'] = array('label'=>lang('ARCHIVE'), 'colwidth'=>'10%', 'sort'=>True, 'type'=>'yes_no'); 		$data['fields']['modifyDT'] = array('label'=>lang('MODIFYDT'), 'colwidth'=>'20%', 'sort'=>True); 		$data['tools'] = $this->get_tools(); 		$data['ds'] = $this->advertisescompanies_model->get_ds();		$data['paging'] = $this->get_paging($this->advertisescompanies_model->get_calc_rows());		return $this->load->view('standart/grid', $data, True);			}	private function create(){		$data = array();		$this->set_validation();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_CREATE');		$data['orid'] = $this->get_orid();		$data['success'] = null;		if ($this->form_validation->run() === True){			if($rid = $this->advertisescompanies_model->create_record()){				$this->session->set_flashdata('success', True);				redirect(get_currcontroller()."/edit/$rid", 'refresh');				return;			}			else {				$data['success'] = false;			} 		}		$data['content'] = $this->load->view('advertisescompanies/create', $data, True);		return $this->load->view('layouts/main_layout', $data);	}	private function edit(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$this->set_validation();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_EDIT');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;		if(!$data['ds']) show_404(); 		if ($this->form_validation->run() === True){			if($this->advertisescompanies_model->update_record()) $data['success'] = true;			else $data['success'] = false;			$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		}		$data['content'] = $this->load->view('advertisescompanies/edit', $data, True);		return $this->load->view('layouts/main_layout', $data);	}	private function details(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_DETAILS');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		if(!$data['ds']) show_404(); 		$data['content'] = $this->load->view('advertisescompanies/details', $data, True);		return $this->load->view('layouts/main_layout', $data);	}	private function find(){		$data['orid'] = $this->get_orid();		$this->form_validation->set_rules('company_type', lang('COMPANY_TYPE'), 'trim');		$this->form_validation->set_rules('company_name', lang('COMPANY'), 'trim');		$this->form_validation->set_rules('archive', lang('HIDE_ARCHIVE'), 'trim');				if ($this->form_validation->run() == True){			$search_rule = array();			if($this->input->post('company_type')) $search_rule['like']['company_type'] = $this->input->post('company_type');			if($this->input->post('company_name')) $search_rule['like']['company_name'] = $this->input->post('company_name');			if($this->input->post('archive')==0) $search_rule['where']['archive'] = $this->input->post('archive');			$this->set_searchrule($search_rule);		}		$search = $this->get_session('searchrule');		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));		return $this->load->view('advertisescompanies/find', $data, True);	}	public function check_unique_name($code){		$rid = $this->input->post('rid'); # для случая если проверка идет при редактировании		if($this->advertisescompanies_model->check_unique($code, 'name', $rid)){			$this->form_validation->set_message('check_unique_name', lang('advertisescompanies_NAME_NOTUNIQUE'));			return False;		}		return True;	}	private function move(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_MOVE');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;		if(!$data['ds']) show_404(); 		if ($this->form_validation->run() === True){			if($this->advertisescompanies_model->move_record()) $data['success'] = true;			else $data['success'] = false;			$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		}		$data['content'] = $this->load->view('standart/move', $data, True);		return $this->load->view('layouts/main_layout', $data);	}		/* Операции для Value Picker */	private function vcreate(){		$data = array();		$this->set_validation();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_CREATE');		$data['orid'] = $this->get_orid();		$data['success'] = null;		if ($this->form_validation->run() === True){			if($rid = $this->advertisescompanies_model->create_record()){				$this->session->set_flashdata('success', True);				redirect(get_currcontroller()."/vedit/$rid", 'refresh');				return;			}			else {				$data['success'] = false;			} 		}		$data['content'] = $this->load->view('advertisescompanies/vcreate', $data, True);		return $this->load->view('layouts/valuepicker_layout', $data);	}	public function vjournal(){		$data = array();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE');		$data['orid'] = $this->get_orid();		$data['sort'] = $this->get_session('sort');		$data['find'] = $this->vfind();		$data['fields']['rid'] = array('label'=>'Id', 'colwidth'=>'5%', 'sort'=>True); 		$data['fields']['company_name'] =  array('label'=>lang('COMPANY'), 'colwidth'=>'35%', 'sort'=>True);		$data['fields']['company_type'] =  array('label'=>lang('COMPANY_TYPE'), 'colwidth'=>'35%', 'sort'=>True);		$data['fields']['archive'] = array('label'=>lang('ARCHIVE'), 'colwidth'=>'10%', 'sort'=>True, 'type'=>'yes_no'); 		$data['fields']['modifyDT'] = array('label'=>lang('MODIFYDT'), 'colwidth'=>'20%', 'sort'=>True); 		$data['jtp'] = $this->jtp;		$data['tools'] = $this->get_tools(); 		$data['ds'] = $this->advertisescompanies_model->get_ds();		$data['paging'] = $this->get_paging($this->advertisescompanies_model->get_calc_rows(), True);		$content = $this->load->view('standart/vgrid', $data, True);		$this->load->view('layouts/valuepicker_layout', array('content'=>$content));				return;	}		private function vedit(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$this->set_validation();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_EDIT');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;		$data['jtp'] = $this->jtp;		if(!$data['ds']) show_404(); 		if ($this->form_validation->run() === True){			if($this->advertisescompanies_model->update_record()) $data['success'] = true;			else $data['success'] = false;			$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		}		$data['content'] = $this->load->view('advertisescompanies/vedit', $data, True);		return $this->load->view('layouts/valuepicker_layout', $data);	}	private function vdetails(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_DETAILS');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['jtp'] = $this->jtp;		$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		if(!$data['ds']) show_404(); 		$data['content'] = $this->load->view('advertisescompanies/vdetails', $data, True);		return $this->load->view('layouts/valuepicker_layout', $data);	}	private function vfind(){		$data['orid'] = $this->get_orid();		$this->form_validation->set_rules('company_type', lang('COMPANY_TYPE'), 'trim');		$this->form_validation->set_rules('company_name', lang('COMPANY'), 'trim');		$this->form_validation->set_rules('archive', lang('HIDE_ARCHIVE'), 'trim');				if ($this->form_validation->run() == True){			$search_rule = array();			if($this->input->post('company_type')) $search_rule['like']['company_type'] = $this->input->post('company_type');			if($this->input->post('company_name')) $search_rule['like']['company_name'] = $this->input->post('company_name');			if($this->input->post('archive')==0) $search_rule['where']['archive'] = $this->input->post('archive');			$this->set_searchrule($search_rule);		}		$search = $this->get_session('searchrule');		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));		return $this->load->view('advertisescompanies/vfind', $data, True);	}		private function vmove(){		$rid = (int)$this->uri->segment(3);		if(!$rid) show_404();		$data = array();		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');		$data['title'] = lang('ADVERTISESCOMPANIES_TITLE_MOVE');		$data['rid'] = $rid;		$data['orid'] = $this->get_orid();		$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;		if(!$data['ds']) show_404(); 		if ($this->form_validation->run() === True){			if($this->advertisescompanies_model->move_record()) $data['success'] = true;			else $data['success'] = false;			$data['ds'] = $this->advertisescompanies_model->get_edit($rid);		}		$data['content'] = $this->load->view('standart/vmove', $data, True);		return $this->load->view('layouts/valuepicker_layout', $data);	}		private function set_validation(){		$this->form_validation->set_rules('company_name', lang('COMPANY'), 'required|trim');		$this->form_validation->set_rules('company_type', lang('COMPANY_TYPE'), 'required');		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');		return;	}		}	?>