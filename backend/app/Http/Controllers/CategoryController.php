<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    public function index(){
        $categories = Category::all();

        if(!$categories) {
            return $this->failedResponse('Failed to retrieve categories.',404);
        }
        return $this->successResponse(
            'Categories retrieved.',
            200,
            ['categories' => $categories],
        );
    }
    public function show($id) {
        $category = Category::findOrFail($id);

        if(!$category){
            return $this->failedResponse('Failed to retrieve category.',404);
        }
        return $this->successResponse('Category retrieved.',200);
    }
}
