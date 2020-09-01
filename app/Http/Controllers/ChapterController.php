<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chapter;
use App\Course;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function index(Request $request) {
        $chapters = Chapter::query();
        
        // Filter by Course Id
        $courseId = $request->query('course_id');

        // Query where
        $chapters->when($courseId, function($query) use ($courseId){
            return $query->where('course_id', '=', $courseId);
        });

        return response()->json([
            'status' => 'success',
            'data' => $chapters->get()
        ]);
    }

    public function show($id) {
        // Cari id di DB
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            "data" => $chapter
        ]);
    }

    public function create(Request $request) {
        // Skema validasi
        $rules = [
            'name' => 'required|string',
            'course_id' => 'required|integer'
        ];

        // Ambil data dari request body
        $data = $request->all();

        // Cek Validator antara data dan rules/skema
        $validator = Validator::make($data, $rules);
        
        // Cek error validator
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Ambil course_id di body request
        $courseId = $request->input('course_id');
        // Cek course_id di DB ada atau tidak
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }
        
        // Create data chapter
        $chapter = Chapter::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function update(Request $request, $id) {
        // Skema validasi
        $rules = [
            'name' => 'string',
            'course_id' => 'integer'
        ];

        // Ambil data dari request body
        $data = $request->all();

        // Cek Validator antara data dan rules/skema
        $validator = Validator::make($data, $rules);
        
        // Cek error validator
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Cari chapter id di DB
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        // Ambil data request course_id untuk di cek ada atau tidak di DB
        $courseId = $request->input('course_id');
        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'course not found'
                ], 404);
            }
        }

        // Update data di DB
        $chapter->fill($data);
        $chapter->save();

        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function destroy($id) {
        // Cari id di DB
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        // Delete data by $id di DB
        $chapter->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'chapter deleted'
        ]);
    }
}
