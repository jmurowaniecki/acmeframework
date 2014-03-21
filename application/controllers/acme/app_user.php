<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* --------------------------------------------------------------------------------------------------
*
* Controller App_User
* 
* Módulo de usuários da aplicação.
*
* @since	13/08/2012
*
* --------------------------------------------------------------------------------------------------
*/
class App_User extends ACME_Module_Controller {
	
	public $photos_dir = 'user_photos';
	
	/**
	* __construct()
	* Construtor de classe.
	* @return object
	*/
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	/**
	* index()
	* Entrada do módulo.
	* @return void
	*/
	public function index()
	{		
		$this->validate_permission('ENTER');
		
		$args['users'] = $this->app_user_model->get_users();
		
		$this->template->load_page('_acme/app_user/index', $args);
	}

	/**
	* groups()
	* Grupos de usuários.
	* @return void
	*/
	public function groups()
	{		
		$this->validate_permission('ENTER');
		
		$args['groups'] = $this->db->get('acm_user_group')->result_array();
		
		$this->template->load_page('_acme/app_user/groups', $args);
	}

	/**
	* save_group()
	* Insert, update and delete for groups. Fowarded by groups page, through ajax. Operation must
	* be sent as parameter and data by $_POST. Print json as result operation status.
	* @param string operation		// insert, update, delete
	* @return void
	*/
	public function save_group($operation = '')
	{
		if( ! $this->check_permission('ENTER')) {
			echo json_encode(array('return' => false, 'error' => lang('Ops! Você não tem permissão para fazer isso')));
			return;
		}

		switch(strtolower($operation)) {
			
			case 'insert':
			case 'update':
			
				$data['name'] = $this->input->post('name');
				$data['description'] = $this->input->post('description');

				if( strtolower($operation) == 'insert' )
					$this->db->insert('acm_user_group', $data);
				else 
					$this->db->update('acm_user_group', $data, array('id_user_group' => $this->input->post('id_user_group')));

			break;

			case 'delete';
				// there are users in this group!
				if( $this->db->get_where('acm_user', array('id_user_group' => $this->input->post('id_user_group')))->num_rows() > 0 ) {
					echo json_encode(array('return' => false, 'error' => lang('Ops! Existem usuários neste grupo')));
					return;
				} 

				$this->db->delete('acm_user_group', array('id_user_group' => $this->input->post('id_user_group')));
			break;
		}

		// Adorable return!
		echo json_encode(array('return' => true));
	}

	/**
	* new_user()
	* Tela de novo usuário.
	* @param boolean process
	* @return void
	*/
	public function new_user($process = false)
	{		
		$this->validate_permission('INSERT');
		
		// New user form
		if( ! $process) {
			
			$groups = $this->db->select('id_user_group, name')->from('acm_user_group')->order_by('name')->get()->result_array();
			
			$args['options'] = $this->form->build_select_options($groups);

			$this->template->load_page('_acme/app_user/new_user', $args);

		} else {

			// Proccess form
			$user_data['id_user_group'] = $this->input->post('id_user_group');
			$user_data['name'] = $this->input->post('name');
			$user_data['email'] = $this->input->post('email');
			$user_data['description'] = $this->input->post('description');
			$user_data['password'] = md5($this->input->post('password'));

			// insert user
			$this->db->insert('acm_user', $user_data);

			// get user in order to insert configs
			$user = $this->db->get_where('acm_user', array('email' => $user_data['email']))->row_array(0);

			// insert configs to this user
			$config['id_user'] = get_value($user, 'id_user');
			$config['url_default'] = $this->input->post('url_default');
			$config['lang_default'] = $this->input->post('lang_default');

			$this->db->insert('acm_user_config', $config);

			// redirect to config page
			redirect('app_user');
		}
	}

	/**
	* edit()
	* Update user form.
	* @param int id_user
	* @param boolean process
	* @return void
	*/
	public function edit($id_user = 0, $process = false)
	{		
		$this->validate_permission('UPDATE');

		if($id_user == 0 || $id_user == '')
			redirect('app_user');
		
		// New user form
		if( ! $process) {
			
			$args['user'] = $this->app_user_model->get_user($id_user);

			$groups = $this->db->select('id_user_group, name')->from('acm_user_group')->order_by('name')->get()->result_array();
			
			$args['options'] = $this->form->build_select_options($groups, get_value($args['user'], 'id_user_group'));

			$this->template->load_page('_acme/app_user/edit', $args);

		} else {

			// Proccess form
			$user_data['id_user_group'] = $this->input->post('id_user_group');
			$user_data['name'] = $this->input->post('name');
			$user_data['email'] = $this->input->post('email');
			$user_data['description'] = $this->input->post('description');
			$user_data['password'] = md5($this->input->post('password'));

			// update user
			$this->db->update('acm_user', $user_data, array('id_user' => $id_user));

			// configs update
			$config['url_default'] = $this->input->post('url_default');
			$config['lang_default'] = $this->input->post('lang_default');

			$this->db->update('acm_user_config', $config, array('id_user' => $id_user));

			// redirect to config page
			redirect('app_user');
		}
	}

	/**
	* permissions()
	* Permission manager for refered user id.
	* @param integer id_user
	* @return void
	*/
	public function permissions($id_user = 0)
	{
		$this->validate_permission('PERMISSION_MANAGER');

		if($id_user == 0 || $id_user == '')
			redirect('app_user');
		
		$args['permissions'] =  $this->app_user_model->get_permissions($id_user);
		
		$args['user'] = $this->app_user_model->get_user($id_user);
		
		// Carrega view
		$this->template->load_page('_acme/app_user/permissions', $args);
	}

	/**
	* check_email()
	* Validate an email passed by POST, checking if already exist an user with this email.
	* @return void
	*/
	public function check_email()
	{
		$email = $this->input->post('email');

		if( $this->db->get_where('acm_user', array('email' => $email))->num_rows() > 0)
			echo json_encode(array('return' => true));
		else
			echo json_encode(array('return' => false));
	}

	/**
	* save_permission()
	* ENable or disable permission for user. Fowarded by permissions page, through ajax. Operation must
	* be sent as parameter and data by $_POST. Print json as result operation status.
	* @param string operation		// enable, disable
	* @return void
	*/
	public function save_permission($operation = '')
	{
		if( ! $this->check_permission('PERMISSION_MANAGER')) {
			echo json_encode(array('return' => false, 'error' => lang('Ops! Você não tem permissão para fazer isso')));
			return;
		}

		$data['id_user'] = $this->input->post('id_user');
		$data['id_module_permission'] = $this->input->post('id_module_permission');

		// always delete permission to save another
		$this->db->delete('acm_user_permission', $data);
		
		if( strtolower($operation) == 'enable' )
			$this->db->insert('acm_user_permission', $data); 

		// Adorable return!
		echo json_encode(array('return' => true));
	}

	/**
	* profile()
	* Tela de perfil de usuário.
	* @param integer id_user
	* @return void
	*/
	public function profile($id_user = 0)
	{
		// only the logged user can see your profile
		if($this->session->userdata('id_user') != $id_user || $id_user == '' || $id_user == 0)
			redirect($this->session->userdata('url_default'));

		// load user data
		$args['user'] = $this->app_user_model->get_user($id_user);
		
		// browser ranking access
		$browser_rank = $this->app_user_model->browser_rank_user($id_user);
		$args['browser_rank'] = isset($browser_rank[0]) ? $browser_rank : array(0 => array());
		
		// load view
		$this->template->load_page('_acme/app_user/profile', $args);
	}

	/**
	* edit_profile()
	* Form and process form of user profile.
	* @param integer id_user
	* @param boolean process
	* @return void
	*/
	public function edit_profile($id_user = 0, $process = false)
	{		
		// only the logged user can see your profile
		if($this->session->userdata('id_user') != $id_user || $id_user == '' || $id_user == 0)
			redirect($this->session->userdata('url_default'));

		if( ! $process) {
			
			// load user data
			$args['user'] = $this->app_user_model->get_user($id_user);
						
			// load view
			$this->template->load_page('_acme/app_user/edit_profile', $args);
		} else {

			// update user
			$user['name'] = $this->input->post('name');
			$user['email'] = $this->input->post('email');
			$user['description'] = $this->input->post('description');
			$this->db->update('acm_user', $user, array('id_user' => $id_user ));

			// Array de atualização de config de user
			$config['lang_default'] = $this->input->post('lang_default');
			$this->db->update('acm_user_config', $config, array('id_user' => $id_user ));
			
			// redirect to profile
			redirect('app_user/profile/' . $id_user);
		}
	}

	/**
	* edit_photo()
	* Form to edit thumbnail image, upload, resize, etc.
	* @param integer id_user
	* @return void
	*/
	public function edit_photo($id_user = 0)
	{
		// only the logged user can see your profile
		if($this->session->userdata('id_user') != $id_user || $id_user == '' || $id_user == 0)
			redirect($this->session->userdata('url_default'));
			
		// load user data
		$args['user'] = $this->app_user_model->get_user($id_user);	
		
		// load view
		$this->template->load_page('_acme/app_user/edit_photo', $args);
	}

	/**
	* upload_photo()
	* Upload image and update on database, for current user.
	* @param int id_user
	* @return void
	*/
	public function upload_photo($id_user = 0)
	{
		// only the logged user can see your profile
		if($this->session->userdata('id_user') != $id_user || $id_user == '' || $id_user == 0)
			redirect($this->session->userdata('url_default'));

		// upload configs
		$config['upload_path'] = PATH_UPLOAD . '/' . $this->photos_dir;
		$config['file_name'] = $id_user . '_' . uniqid();
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_width'] = '4500';
		$config['max_height'] = '4500';

		// load library to upload
		$this->load->library('upload', $config);
		
		// try to upload 
		if ( ! $this->upload->do_upload('file'))
		{
			// yeah, we have errors
			$return = $this->upload->display_errors("<error>", "</error>");
				
			// force an header 400 (error)
			$this->output->set_status_header('400');
		} 
		
		// Upload ok!!
		else {
			// user data
			$user = $this->app_user_model->get_user($id_user);
			
			// name of new image 
			$new_file = get_value($this->upload->data(), 'file_name');
			
			// remove the previous user image
			@unlink(PATH_UPLOAD . '/' . $this->photos_dir . '/' . basename(get_value($user, 'url_img_large')));

			// update currently image
			$this->db->update('acm_user_config', array('url_img_large' => '{URL_UPLOAD}/' . $this->photos_dir . '/' . $new_file), array('id_user' => $id_user));

			// new image to return
			$return = $new_file;
		}

		echo $return;
	}

	/**
	* edit_thumbnail()
	* update user thumbnail according to the coordinates.
	* @return int id_user
	* @return void
	*/
	public function edit_thumbnail($id_user = 0)
	{
		// only the logged user can see your profile
		if($this->session->userdata('id_user') != $id_user || $id_user == '' || $id_user == 0)
			redirect($this->session->userdata('url_default'));

		// coordinates of image
		$w = $this->input->post('w');
		$h = $this->input->post('h');
		$sw = $this->input->post('sw');
		$sh = $this->input->post('sh');
		$x1 = $this->input->post('x1');
		$x2 = $this->input->post('x2');
		$y1 = $this->input->post('y1');
		$y2 = $this->input->post('y2');

		// Make thumb only if coordinates are correct
		if($w != '' && $h != '' && $x1 != '' && $x2 != '' && $y1 != '' && $y2 != '' && $sw != '' && $sh != '')
		{
			// user data
			$user = $this->app_user_model->get_user($id_user);
			
			// make thumb (return = thumb name)
			if(($file_thumb_name = $this->_make_thumbnail($id_user, basename(get_value($user, 'url_img_large')), $w, $h, $sw, $sh, $x1, $x2, $y1, $y2)) === false)
			{
				// force header 400 (erro)
				$this->output->set_status_header('400');
			} else {
				// Remove previous thumb
				@unlink(PATH_UPLOAD . '/' . $this->photos_dir . '/' . basename(get_value($user, 'url_img')));

				// update info on database
				$new_user_img = '{URL_UPLOAD}/' . $this->photos_dir . '/' . $file_thumb_name;
				$this->db->update('acm_user_config', array('url_img' => $new_user_img), array('id_user' => $id_user));
				
				// update img on session
				$this->session->set_userdata('user_img', tag_replace($new_user_img));
			}
		}
	}

	/**
	* _make_thumbnail()
	* Make a thumbnail from de coordinates and user info.
	* @param string img
	* @param int rw width of image resized (responsive)
	* @param int rh height of image resized (responsive)
	* @param int sw width of thumb selection
	* @param int sh height of thumb selection
	* @param int x1
	* @param int x2
	* @param int y1
	* @param int y2
	* @return mixed string filename / boolean error
	*/
	private function _make_thumbnail($id_user = 0, $lrg_image = '', $rw = 0, $rh = 0, $sw = 0, $sh = 0, $x1 = 0, $x2 = 0, $y1 = 0, $y2 = 0)
	{
		// Configs. of thumb
		$tw = 150;
		$th = 150;
		$tmb_ext = 'png';
		$tmb_file_name = $id_user . '_tmb_' . uniqid() . '.' . $tmb_ext;
		$tmb_path_file = PATH_UPLOAD . '/' . $this->photos_dir . '/' . $tmb_file_name;

		// original img info (original, large)
		$lrg_path_file = PATH_UPLOAD . '/' . $this->photos_dir . '/' . $lrg_image;
		list($ow, $oh, $img_type) = getimagesize($lrg_path_file);

		// recalculates points (scale, 3 rule)
		$nx1 = $ow * $x1 / $rw;
		$nx2 = $ow * $x2 / $rw;
		$ny1 = $oh * $y1 / $rh;
		$ny2 = $oh * $y2 / $rh;
		$nsw = $ow * $sw / $rw;
		$nsh = $oh * $sh / $rh;

		$img_type = image_type_to_mime_type($img_type);
		
		// transforms original img into a source
		switch($img_type) 
		{
			case 'image/gif':
				$lrg_image_source = imagecreatefromgif($lrg_path_file); 
			break;

			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$lrg_image_source = imagecreatefromjpeg($lrg_path_file); 
			break;

			case 'image/png':
			case 'image/x-png':
			default:
				$lrg_image_source = imagecreatefrompng($lrg_path_file); 
			break;
		}
		
		// create the thumb according to height / width 
		$tmb_source = imagecreatetruecolor($tw, $th);

		// copy the selection info to thumbnail source
		imagecopyresampled($tmb_source, $lrg_image_source, 0, 0, $nx1, $ny1, $tw, $th, $nsw, $nsh);
		
		// makes thumb on refered filepath
		if(imagepng($tmb_source, $tmb_path_file) === true)
			return $tmb_file_name;
		else
			return false;
	}
	
	/**
	* reset password()
	* Send an email to user by refered id, this email contains the steps to reset password.
	* @param int id_user
	* @return void
	*/
	public function reset_password($id_user = 0)
	{
		if( ! $this->check_permission('RESET_PASSWORD')) {
			echo json_encode(array('return' => false, 'error' => lang('Ops! Você não tem permissão para fazer isso')));
			return;
		}

		// user data
		$user = $this->app_user_model->get_user($id_user);
		$args['user'] = $user;
		$args['id_user'] = $id_user;
		$args['key_access'] = md5(uniqid());

		// collect email body msg
		$body_msg = $this->template->load_page('_acme/app_user/email_reset_password', $args, true, false);
		
		// now try to send email
		$this->load->library('email');
		$this->email->clear();
	    $this->email->to(get_value($user, 'email'));
	    $this->email->from(EMAIL_FROM, APP_NAME);
	    $this->email->subject(lang('Alteração de senha'));
	    $this->email->message($body_msg);
	    
	    if( ! @$this->email->send() ) {
			echo json_encode(array('return' => false, 'error' => lang('Ops! Não foi possível enviar a mensagem de email. Verifique as configurações de email da aplicação.')));
			return;
		}

		// log asking for reset pass
		$data['id_user'] = $id_user;
		$data['key_access'] = $args['key_access'];
		$data['updated'] = false;

		$this->log->db_log(lang('Solicitação de Alteração de Senha'), 'reset_password', '', $args);
		
		// Adorable return!
		echo json_encode(array('return' => true));
	}
	
	/**
	* ajax_copy_permissions()
	* Modal de cópia de permissões de um determinado usuário para o usuário de id encaminhado.
	* @param integer id_user
	* @return void
	*/
	public function ajax_copy_permissions($id_user = 0)
	{
		// Valida permissão
		$args['permission'] = $this->validate_permission('COPY_PERMISSIONS', false);
		
		// Dados do usuário
		$args['user'] = $this->app_user_model->get_user_data($id_user);
		
		// Dados de opções de usuário
		$args['user_options'] = $this->form->build_array_html_options($this->app_user_model->get_users_to_html_options());
		
		// Variável para teste de caso usuario nao seja root e esteja tentando acessar uma copia para um
		$args['editable'] = ($this->session->userdata('user_group') != 'ROOT' && get_value($args['user'], 'group_name') == 'ROOT') ? false : true;
		
		// Carrega view
		$this->template->load_page('_acme/app_user/ajax_copy_permissions', $args, false, false);
	}
	
	/**
	* ajax_copy_permissions_process()
	* Processa modal de cópia de permissões de um determinado usuário para outro, ambos de id
	* encaminhado por post.
	* @return void
	*/
	public function ajax_copy_permissions_process()
	{
		$id_user_to = $this->input->post('id_user_to');
		$id_user_from = $this->input->post('id_user_from');
		if($this->validate_permission('COPY_PERMISSIONS', false) && $id_user_from != '' && $id_user_to != '')
		{
			// Deleta permissões anteriores do usuário PARA
			$this->db->where(array('id_user' => $id_user_to));
			$this->db->delete('acm_user_permission');
			
			// Copia permissões
			$this->app_user_model->copy_permissions($id_user_from, $id_user_to);
			
			// Carrega view
			$this->template->load_page('_acme/app_user/ajax_copy_permissions_process', array(), false, false);
		}		
	}
}
