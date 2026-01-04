<?php
class CartController extends BaseController {
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->paintingModel = new Painting();
    }
    
    public function index() {
        try {
            $this->view('cart/index');
        } catch (Exception $e) {
            ErrorHandler::logError([
                'type' => 'CONTROLLER_ERROR',
                'message' => $e->getMessage(),
                'controller' => 'CartController',
                'action' => 'index'
            ]);
            $this->view('errors/500');
        }
    }
}
?>