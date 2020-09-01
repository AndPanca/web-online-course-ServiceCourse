<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Mentor;
use App\Review;
use App\MyCourse;
use App\Chapter;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request){
        // Get data course berdasarkan query -> untuk set paginate
        $courses = Course::query();

        // Fitur search data dengan nama, berdasarkan Query Params: search
        $search = $request->query('search');
        // Query where search LIKE
        $courses->when($search, function($query) use ($search){
            return $query->whereRaw("name LIKE '%".strtolower($search)."%'");
        });

        // Fitur search berdasarkan status course, berdasarkan Query Params: status
        $status = $request->query('status');
        // Query where equal atau samadengan
        $courses->when($status, function($query) use ($status){
            return $query->where('status', '=', $status);
        });
        

        return response()->json([
            'status' => 'success',
            // Set per Page 10 courses
            'data' => $courses->paginate(10)
        ]);
    }

    public function show($id) {
        // with(chapters.lessons) itu mengarah ke Model Chapter kemudian method lessons
        $course = Course::with('chapters.lessons')
                        ->with('mentor')
                        ->with('images')
                        ->find($id);
        

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        // Get data review dari tabel review
        $reviews = Review::where('course_id', '=', $id)->get()->toArray();

        // Get detail user id di review
        if (count($reviews)>0) {
            // Get userIds dari reviews
            $userIds = array_column($reviews, 'user_id');
            $users = getUserByIds($userIds);
            // echo "<pre>".print_r($users,1)."</pre>";
            if ($users['status'] === 'error') {
                $reviews = [];
            } else {
                foreach($reviews as $key => $review) {
                    // Array_search untuk cari key/review berdasarkan value suatu array. Array column cari value brdsarkan key
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    // Inject data user yang sesuai ke reviews
                    $reviews[$key]['users'] = $users['data'][$userIndex];
                }
            }
        }

        // Get total student yg join kelas
        $totalStudent = MyCourse::where('course_id', '=', $id)->count();

        // Cari total videos
        $totalVideos = Chapter::where('course_id', '=', $id)->withCount('lessons')->get()->toArray();
        $finalTotalVideos = array_sum(array_column($totalVideos, 'lessons_count'));

        // Add data review dan total student ke course
        $course['reviews'] = $reviews;
        $course['total_videos'] = $finalTotalVideos;
        $course['total_student'] = $totalStudent;

        return response()->json([
            'status' => 'success', 
            'data' => $course
        ]);
    }

    public function create (Request $request) {
        // Skema Validasi
        $rules = [
            'name' => 'required|string',
            'certificate' => 'required|boolean',
            'thumbnail' => 'string|url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'integer',
            'level' => 'required|in:all-level,beginner,intermediate,advance',
            'mentor_id' => "required|integer",
            'description' => 'string'
        ];

        // Ambil data dari body
        $data = $request->all();

        // Validasi request 
        $validator = Validator::make($data, $rules);

        // Cek error validator
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Ambil mentor_id di body request
        $mentorId = $request->input('mentor_id');
        // Cek mentorId di DB ada atau tidak
        $mentor = Mentor::find($mentorId);

        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        // Create data ke DB
        $course = Course::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function update(Request $request, $id){
        // Skema Validasi
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'string|url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advance',
            'mentor_id' => "integer",
            'description' => 'string'
        ];

        // Ambil data dari body
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

        // Cek Course Id apakah ada di DB
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        // Cek mentor_id yang di update request dari FE ada atau tidak di DB
        $mentorId = $request->input('mentor_id');
        if ($mentorId) {
            $mentor = Mentor::find($mentorId);
            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'mentor not found'
                ], 404);
            }
        }

        // Update data 
        $course->fill($data);
        $course->save();

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function destroy($id) {
        // Cari id di DB
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                "message" => 'course not found'
            ], 404);
        }

        // Delete course
        $course->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'course deleted'
        ]);
    }
}
