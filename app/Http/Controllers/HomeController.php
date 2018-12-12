<?php

namespace App\Http\Controllers;

use Analytics;
use Spatie\Analytics\Period;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //die('sdfdf');
        $analyticsData = Analytics::performQuery(
            Period::years(1),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions, ga:pageviews',
                'dimensions' => 'ga:yearMonth',
                'filters' => 'ga:pagePath==/lux/public/property_view/techno-world/eyJpdiI6ImZvUVYxVE1Ia3BqV3ZvQVVRZmRONEE9PSIsInZhbHVlIjoid09lMkwxV0ZWOGZIdmZyR3BlZFNjUT09IiwibWFjIjoiNzQyN2E4ZDAyZTVmNzQ5MGZhYzFhNmQyMDUwNDQwZGUwZDlmMzZlODU5NjQyYjAzYjdiNTk4ODQ0YTg5MjRiMSJ9',
            ]
        );
        print_r($analyticsData);die('asd');
        return view('home');
    }
}
