<?php
class ArtistController extends BaseController {
    private $artistModel;
    private $paintingModel;
    
    public function __construct() {
        parent::__construct();
        $this->artistModel = new Artist();
        $this->paintingModel = new Painting();
    }
    
    public function index() {
        try {
            $artists = $this->artistModel->getActiveWithPaintingCount();
            $this->view('artists/index', ['artists' => $artists]);
        } catch (Exception $e) {
            ErrorHandler::logError([
                'type' => 'CONTROLLER_ERROR',
                'message' => $e->getMessage(),
                'controller' => 'ArtistController',
                'action' => 'index'
            ]);
            $this->view('errors/500');
        }
    }
    
    public function paintings($artistId) {
        try {
            $artist = $this->artistModel->find($artistId);
            if (!$artist || $artist['status'] !== 'active') {
                $this->view('errors/404');
                return;
            }
            
            $paintings = $this->paintingModel->getByArtist($artistId);
            $this->view('artists/paintings', [
                'artist' => $artist,
                'paintings' => $paintings
            ]);
        } catch (Exception $e) {
            ErrorHandler::logError([
                'type' => 'CONTROLLER_ERROR',
                'message' => $e->getMessage(),
                'controller' => 'ArtistController',
                'action' => 'paintings'
            ]);
            $this->view('errors/500');
        }
    }
}
?>