<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Attraction;

class AttractionController extends Controller
{
    private Attraction $attractionModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->layoutPath = BASE_PATH . '/App/Views/Layouts/client-layout.php';
        $this->attractionModel = new Attraction();
    }

    public function index(): void
    {
        $attractions = $this->attractionModel->getAllOrdered();
        
        $this->useLayout = true;
        // Assuming client-layout is used
        if (!file_exists($this->layoutPath)) {
            $this->useLayout = false;
        }

        $this->render('Attractions/index', [
            'title' => 'Khám phá Điểm đến (Attractions)',
            'attractions' => $attractions,
        ]);
    }
}

