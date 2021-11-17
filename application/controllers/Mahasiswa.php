<?php
defined('BASEPATH') or exit('no direct script allowed here');
require APPPATH . '/libraries/REST_Controller.php';
use RestServer\Libraries\REST_Controller;

class Mahasiswa extends REST_Controller
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
    }

    public function index_get()
    {
        // var_dump($this->get());
        $id = $this->get('id');
        $mahasiswa = [];

        if ($id == '') {
            $data = $this->db->get('mahasiswa')->result();
            // var_dump($data);
            foreach ($data as $row => $key):
                $mahasiswa[] = [
                    "id" => $key->id,
                    "nim" => $key->nim,
                    "nama" => $key->nama,
                    "_links" => (object) [
                        "href" => "prodi/{$key->id_prodi}",
                        "rel" => "prodi",
                        "type" => "GET",
                    ],
                    "angkatan" => $key->angkatan,
                    "jenis_kelamin" => $key->jenis_kelamin,
                    "tempat_lahir" => $key->tempat_lahir,
                    "tanggal_lahir" => $key->tanggal_lahir,
                    "batas_studi" => $key->batas_studi,
                ];
            endforeach;
        } else {
            $this->db->where('id', $id);
            $mahasiswa = $this->db->get('mahasiswa')->result();
        }
        $result = [
            "took" => $_SERVER['REQUEST_TIME_FLOAT'],
            "code" => 200,
            "message" => "Response Successfully",
            "data" => $mahasiswa,
        ];
        $this->response($result, 200);
    }

    public function index_post()
    {
        if ($this->post()) { // cek apakah ada data post, bila tidak ada reply pesan fail
            $data = array(
                "nim" => $this->post('nim'),
                "nama" => $this->post('nama'),
                "angkatan" => $this->post('angkatan'),
                "jenis_kelamin" => $this->post('jenis_kelamin'),
                "tempat_lahir" => $this->post('tempat_lahir'),
                "tanggal_lahir" => $this->post('tanggal_lahir'),
                "batas_studi" => $this->post('batas_studi'),
                "id_prodi" => $this->post('id_prodi'),
            );

            $insert = $this->db->insert('mahasiswa', $data);
            if ($insert) {
                $result = [
                    'took' => $_SERVER['REQUEST_TIME_FLOAT'],
                    'code' => 200,
                    'message' => 'Data has successfully added',
                    'data' => $data,
                ];
                $this->response($result, 201);
            } else {
                $result = [
                    'took' => $_SERVER['REQUEST_TIME_FLOAT'],
                    'code' => 502,
                    'message' => 'Failed adding data',
                    'data' => null,
                ];
                $this->response($result, 502);

            }
        } else {
            $this->response(
                [
                    'status' => 'fail',
                    'message' => 'POST must contain data',
                ],
                502
            );
        }

    }

    public function index_put()
    {
        $id = $this->put('id');
        if ($id) {
            $data = array(
                "nama" => $this->put('nama'),
                "angkatan" => $this->put('angkatan'),
                "jenis_kelamin" => $this->put('jenis_kelamin'),
                "tempat_lahir" => $this->put('tempat_lahir'),
                "tanggal_lahir" => $this->put('tanggal_lahir'),
                "batas_studi" => $this->put('batas_studi'),
                "id_prodi" => $this->put('id_prodi'),
            );
            $this->db->where('id', $id);
            $update = $this->db->update('mahasiswa', $data);
            if ($update) {
                $this->response($data, 200);
            } else {
                $this->response(['status' => 'fail'], 200);
            }
        } else {
            $this->response(['status' => 'fail'], 502);
        }

    }

    public function index_delete()
    {
        $id = $this->delete('id');
        $delete = $this->db->where('id', $id)->delete('mahasiswa');
        if ($delete) {
            $this->response(['status' => 'success'], 201);
        } else {
            $this->response(['status' => 'fail'], 502);
        }
    }

}
