<?php

class UserController extends Controller
{
    private $user;
    private $client;
    public function __construct()
    {
        $this->user = $this->model("User");
        $this->client = $this->model("Client");
    }

    public function index()
    {
        $users = $this->user->all();
        $this->view('user/index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $this->view('user/create', []);
    }

    public function login()
    {
        $this->view('user/login', []);
    }

    public function signup()
    {
        $this->view('user/signup', []);
    }

    public function store()
    {
        if ($this->user->usernameExists($this->input('username'))) {
            $this->session_put('error', 'Username already exists');
            $this->redirect('users/create');
        }

        $form = [
            'username' => $this->input('username'),
            'password' => $this->input('password'),
            'first_name' => $this->input('first_name'),
            'middle_name' => $this->input('middle_name'),
            'last_name' => $this->input('last_name'),
            'dob' => $this->input('dob'),
            'gender' => $this->input('gender'),
            'role' => $this->input('role'),
        ];

        if ($this->user->register($form)) {
            $this->redirect('users');
        } else {
            $this->redirect('users');
        }
    }

    public function register()
    {
        try {
            $this->user->beginTransaction();
            if ($this->user->usernameExists($this->input('username'))) {
                $this->session_put('error', 'Username already exists');
                $this->redirect('signup');
            }

            $fullname = $this->input('first_name') . ' ' . $this->input('middle_name') . '' . $this->input('last_name');
            $form = [
                'username' => $this->input('username'),
                'password' => $this->input('password'),
                'fullname' => $fullname,
            ];

            $user_id = $this->user->register($form, true);
            if (!$user_id) {
                throw new Exception("Error occur");
            }

            $formClient = [
                'user_id' => $user_id,
                'first_name' => $this->input('first_name'),
                'middle_name' => $this->input('middle_name'),
                'last_name' => $this->input('last_name'),
                'address' => $this->input('address'),
                'dob' => $this->input('dob'),
                'gender' => $this->input('gender'),
                'contact_no' => $this->input('contact_no'),
                'email' => $this->input('email'),
                'civil_status' => $this->input('civil_status'),
            ];
            $this->client->add($formClient);

            $this->user->commitTransaction();

            $this->session_put('success', 'Successfully Registered!');
            $this->redirectLogin();
        } catch (Exception $e) {
            $this->user->rollbackTransaction();
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }

    public function authenticate()
    {
        $form = [
            'username' => $this->input('username'),
            'password' => $this->input('password')
        ];
        $redirect = $_SESSION[SYSTEM]['last_url'] ?? 'home';
        if ($this->user->login($form)) {
            $this->redirect($redirect);
        } else {
            // $this->view('register', ['error' => 'Registration failed']);
            $this->session_put('error', 'Account not match!');
            $this->redirectLogin();
        }
    }

    public function logout()
    {
        unset($_SESSION[SYSTEM]);
        session_destroy();
        $this->redirect('login');
    }
}
