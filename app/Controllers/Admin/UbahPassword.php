<?php

namespace App\Controllers\Admin;

use App\Models\PenggunaModel;

class UbahPassword extends BaseController
{
  protected $form_validation = null;
  public function __construct()
  {
    helper('form');
    $this->form_validation =  \Config\Services::validation();
  }
  public function index()
  {
    $data['view'] = 'admin/ubah_password';
    $data['_validation'] = $this->form_validation;
    $data = array_merge($data, $this->data);
    $method = $this->request->getMethod();
    if ($method == "post") {
      return $this->process();
    } else {
      return view($data['view'], $data);
    }
  }

  function process()
  {
    $admin = $this->request->admin;
    $admin_id = $admin->pengguna_id;
    $rule = [
      'password_lama' => [
        'label'  => 'Password Lama',
        'rules'  => "required|old_pass[{$admin_id}]",
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'old_pass' => '{field} tidak valid',
        ]
      ],
      'password_baru' => [
        'label'  => 'Password Baru',
        'rules'  => 'required|matches[konfirmasi_password]',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'matches' => '{field} harus sama dengan {param}'
        ]
      ],
      'konfirmasi_password' => [
        'label'  => 'Konfirmasi Password Baru',
        'rules'  => 'required|matches[password_baru]',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'matches' => '{field} harus sama dengan {param}'
        ]
      ],
    ];
    $data = [
      'password_lama' => htmlspecialchars($this->request->getPost('password_lama')),
      'password_baru' => htmlspecialchars($this->request->getPost('password_baru')),
      'konfirmasi_password' => htmlspecialchars($this->request->getPost('konfirmasi_password')),
    ];
    $this->form_validation->setRules($rule);
    if (!$this->form_validation->run($data)) {
      return redirect()->back()->withInput();
    }
    $penggunaModel = new PenggunaModel();
    if ($penggunaModel->update($admin_id, ['pengguna_password' => $data['password_baru']])) {
      $this->session->setFlashdata('success', 'Password berhasil diubah');
      return redirect()->to(base_url('admin'));
    } else {
      $this->session->setFlashdata('error', 'Gagal mengubah password');
      return redirect()->back()->withInput();
    }
  }

  //--------------------------------------------------------------------

}
