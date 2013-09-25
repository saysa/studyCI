<?php
class Users extends CI_Controller {
	
	public $user;
	
	public static function redirect($url)
	{
		header("Location: {$url}");
		exit();
	}
	
	protected function _isSecure()
	{
		// get user session
		$this->_getUser();
		
		if (!$this->user)
		{
			self::redirect("/login");
		}
	}
	
	protected function _getUser()
	{
		// load session library
		$this->load->library("session");
		
		// get user id
		$id = $this->session->userdata("user");
		
		if ($id)
		{
			// load user model
			$this->load->model("user");
			
			// get user
			$this->user = new User(array(
				"id" => $id
			));
		}
	}
	
	public function register()
	{
		$success = false;
		
		// load validation library
		$this->load->library("form_validation");
		
		// if form was posted
		if ($this->input->post("save"))
		{
			// initialize validation rules
			$this->form_validation->set_rules(array(
				array(
					"field" => "first",
					"label" => "First",
					"rules" => "required|alpha|min_length[3]|max_length[32]"
				),
				array(
					"field" => "last",
					"label" => "Last",
					"rules" => "required|alpha|min_length[3]|max_length[32]"
				),
				array(
					"field" => "email",
					"label" => "Email",
					"rules" => "required|max_length[100]"
				),
				array(
					"field" => "password",
					"label" => "Password",
					"rules" => "required|min_length[8]|max_length[32]"
				)
			));
			
			// if form data passes validation...
			if ($this->form_validation->run())
			{
				// load user model
				$this->load->model("user");
				
				// create new user + save
				$user = new User(array(
					"first"    => $this-> input-> post("first"),
					"last" 	   => $this-> input-> post("last"),
					"email"    => $this-> input-> post("email"),
					"password" => $this-> input-> post("password")
				));
				$user-> save();
				
				// indicate success in view
				$success = true;
			}
		}
		
		// load view
		$this-> load-> view("users/register", array(
			"success" => $success
		));
	}
	
	public function login()
	{
		$errors = null;
		
		// load validation library
		$this->load->library("form_validation");
		
		// if form was posted
		if ($this->input->post("login"))
		{
			// initialize validation rules
			$this->form_validation->set_rules(array(
				array(
					"field" => "email",
					"label" => "Email",
					"rules" => "required|max_length[100]"
				),
				array(
					"field" => "password",
					"label" => "Password",
					"rules" => "required|min_length[8]|max_length[32]"
				)
			));
			
			// load user model
			$this->load->model("user");
			
			// create new user + save
			$user = User::first(array(
				"email" => $this-> input-> post("email"),
				"password" => $this-> input-> post("password"),
				"live" => 1,
				"deleted" => 0
			));
			
			// if form data passes validation...
			if ($user && $this->form_validation->run())
			{
				// load session library
				$this->load->library("session");
				
				// save user id to session
				$this->session->set_userdata("user", $user->id);
				
				// redirect to profile page
				self::redirect("/profile");
			}
			else
			{
				// indicate errors
				$errors = "Email address and/or password are incorrect";
			}
		}
			
		// load view
		$this->load->view("users/login", array(
			"errors" => $errors
		));
	}
	
	public function logout()
	{
		// load session library
		$this->load->library("session");
		
		// remove user id
		$this->session->unset_userdata("user");
		
		// redirect to login
		self::redirect("/login");
	}
	
	public function profile()
	{
		// check for user session
		$this->_isSecure();
		
		// load view
		$this->load->view("users/profile", array(
			"user" => $this->user
		));
	}
}
