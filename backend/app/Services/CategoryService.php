<?php

namespace App\Services;

use App\Models\Category;

class CategoryService {

    protected $category;

    public function __construct(Category $category) {
        $this->category = $category;
    }

    public function index() {
        return $this->category->all();
    }

    public function findById($id){
        return $this->category->findOrFail($id);
    }
}
