<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Mentor;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    public function index() {
        $mentors = Mentor::all();

        return response()->json([
            'status' => 'success',
            'data' => $mentors
        ]);
    }

    public function show($id){
        // Cari id mentor yang akan di update di DB
        $mentor = Mentor::find($id);

        // Jika id tidak ada
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mentor
        ]);
    }

    public function create(Request $request) {
        // Skeman validasi data
        $rules = [
            'name' => 'required|string',
            'profile' => 'required|url',
            'profession' => 'required|string',
            'email' => 'required|email'
        ];

        // Mengambil seluruh data body request
        $data = $request->all();

        // Validasi request ke method create()
        $validator = Validator::make($data, $rules);

        // Cek error validator
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Create data ke DB jika tidak ada error
        $mentor = Mentor::create($data);

        // Mengembalikan data yang di create ke front end
        return response()->json([
            'status' => 'success',
            'data'=> $mentor
        ]);
    }

    public function update(Request $request, $id) {
        // Skeman validasi data
        $rules = [
            'name' => 'string',
            'profile' => 'url',
            'profession' => 'string',
            'email' => 'email'
        ];

        // Mengambil seluruh data body request
        $data = $request->all();

        // Validasi request ke method create()
        $validator = Validator::make($data, $rules);

        // Cek error validator
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        
        // Cari id mentor yang akan di update di DB
        $mentor = Mentor::find($id);

        // Jika id tidak ada
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        // Update data body ke DB
        $mentor->fill($data);
        $mentor->save();

        return response()->json([
            'status' => 'success',
            'data' => $mentor
        ]);
    }

    public function destroy($id){
        // Cari id mentor yang akan di update di DB
        $mentor = Mentor::find($id);

        // Jika id tidak ada
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        $mentor->delete();
        return response()->json([
            'status'=> 'success',
            'message'=> 'mentor deleted'
        ]);
    }
}
