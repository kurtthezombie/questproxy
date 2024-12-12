<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    protected $categoryService;
    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }

    public function index(){
        try {
            $categories = $this->categoryService->index();
            $message = $categories->isEmpty() ? "No categories found." : "All Categories retrieved.";

            return $this->successResponse($message,200,['categories' => $categories],);
        } catch (Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage(),500);
        }
    }

    public function show($id) {
        try {
            $this->categoryService->findById($id);
            return $this->successResponse("Category retrieved.",200);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Category {$id} is not found.",404);
        } catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }
}
