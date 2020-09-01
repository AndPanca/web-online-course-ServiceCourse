<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chapter;
use App\Lesson;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $request) {
        $lessons = Lesson::query();

        $chapterId = $request->query('chapter_id');

        $lessons->when($chapterId, function($query) use ($chapterId){
            return $query->where('chapter_id', '=', $chapterId);
        });

        return response()->json([
            'status' => 'success',
            'data' => $lessons->get()
        ]);
    }

    public function show($id) {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }
    
    public function create(Request $request) {
        // Skeman Validasi
        $rules = [
            'name' => 'required|string',
            'video' => 'required|string',
            'chapter_id' => 'required|integer'
        ];

        // Get data request
        $data = $request->all();
        // Validasi data
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Get chapter_id yang di request
        $chapterId = $request->input('chapter_id');
        // Cari chapter id di DB table Chapter
        $chapter = Chapter::find($chapterId);

        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }
        
        // Push data lesson ke DB
        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function update(Request $request, $id) {
        // Skeman Validasi
        $rules = [
            'name' => 'string',
            'video' => 'string',
            'chapter_id' => 'integer'
        ];

        // Get data request
        $data = $request->all();
        // Validasi data
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Cek lesson id in DB
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        // Get data chapter request and cek in DB
        $chapterId = $request->input('chapter_id');
        if ($chapterId) {
            $chapter = Chapter::find($chapterId);
            if (!$chapter) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'chapter not found'
                ], 404);
            }
        }

        // Update data ke DB
        $lesson->fill($data);
        $lesson->save();

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function destroy($id) {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $lesson->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'lesson deleted'
        ]);
    }
}
